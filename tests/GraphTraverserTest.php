<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Data\Tests;

use PSX\Data\GraphTraverser;
use PSX\Data\Tests\Visitor\VisitorTestCase;
use PSX\Data\Visitor\StdClassSerializeVisitor;
use PSX\Record\Record;

/**
 * GraphTraverserTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GraphTraverserTest extends VisitorTestCase
{
    public function testTraverseObject()
    {
        $record  = $this->getObject();
        $visitor = new StdClassSerializeVisitor();
        $graph   = new GraphTraverser();

        $graph->traverse($record, $visitor);

        $expect = <<<JSON
{
    "id": 1,
    "title": "foobar",
    "active": true,
    "disabled": false,
    "rating": 12.45,
    "age": null,
    "date": "2014-01-01T12:34:47+01:00",
    "href": "http:\/\/foo.com",
    "person": {
        "title": "Foo"
    },
    "category": {
        "general": {
            "news": {
                "technic": "Foo"
            }
        }
    },
    "tags": [
        "bar",
        "foo",
        "test"
    ],
    "entry": [
        {
            "title": "bar"
        },
        {
            "title": "foo"
        }
    ]
}
JSON;

        $actual = json_encode($visitor->getObject(), JSON_PRETTY_PRINT);

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testTraverseArray()
    {
        $record  = $this->getArray();
        $visitor = new StdClassSerializeVisitor();
        $graph   = new GraphTraverser();

        $graph->traverse($record, $visitor);

        $expect = <<<JSON
[
    {
        "id": 1,
        "title": "foobar",
        "active": true,
        "disabled": false,
        "rating": 12.45
    },
    {
        "id": 2,
        "title": "foo",
        "active": false,
        "disabled": false,
        "rating": 12.45
    }
]
JSON;

        $actual = json_encode($visitor->getArray(), JSON_PRETTY_PRINT);

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    /**
     * @dataProvider isObjectProvider
     */
    public function testIsObject($expect, $data)
    {
        $this->assertSame($expect, GraphTraverser::isObject($data));
    }

    public function isObjectProvider()
    {
        return [
            [true, new Record()],
            [true, new \stdClass()],
            [true, ['foo' => 'bar']],
            [false, []],
            [false, ['foo']],
            [false, 'foo'],
            [false, null],
        ];
    }

    /**
     * @dataProvider isArrayProvider
     */
    public function testIsArray($expect, $data)
    {
        $this->assertSame($expect, GraphTraverser::isArray($data));
    }

    public function isArrayProvider()
    {
        return [
            [true, []],
            [true, ['foo']],
            [true, ['foo' => 'bar']],
            [false, new \stdClass()],
            [false, 'foo'],
            [false, null],
        ];
    }

    /**
     * @dataProvider isEmptyProvider
     */
    public function testIsEmpty($expect, $data)
    {
        $this->assertSame($expect, GraphTraverser::isEmpty($data));
    }

    public function isEmptyProvider()
    {
        return [
            [true, new Record()],
            [true, new \stdClass()],
            [true, []],
            [true, ''],
            [true, 0],
            [true, 0.0],
            [true, '0'],
            [true, null],
            [true, false],
            [false, Record::fromArray(['foo' => 'bar'])],
            [false, (object) ['foo' => 'bar']],
            [false, ['foo' => 'bar']],
            [false, 'foo'],
        ];
    }

    public function testTraverseReveal()
    {
        $graph   = new GraphTraverser();
        $visitor = new StdClassSerializeVisitor();
        $record  = new Record('record', [
            'string' => 'foo',
            'stringobject' => new StringObject(),
            'integer' => 1,
            'float' => 0.5,
            'boolean' => true,
            'date' => new \DateTime('2016-02-10 19:00:00'),
            'null' => null,
            'array' => ['foo'],
            'arrayassoc' => ['foo' => 'bar'],
            'object' => (object) ['foo' => 'bar'],
            'record' => new Record('record', ['foo' => 'bar']),
            'jsonobject' => new JsonObject(),
            'arrayobject' => new \ArrayObject(['foo' => 'bar']),
            'iterator' => new \ArrayIterator(['bar']),

            'string_array' => ['foo'],
            'stringobject_array' => [new StringObject()],
            'integer_array' => [1],
            'float_array' => [0.5],
            'boolean_array' => [true],
            'date_array' => [new \DateTime('2016-02-10 19:00:00')],
            // this case is a known problem it should produce an array but 
            // produces an object because the CurveArray::isAssoc returns true
            // for an array where the first value is null
            'null_array' => [null],
            'array_array' => [['foo']],
            'arrayassoc_array' => [['foo' => 'bar']],
            'object_array' => [(object) ['foo' => 'bar']],
            'record_array' => [new Record('record', ['foo' => 'bar'])],
            'jsonobject_array' => [new JsonObject()],
            'arrayobject_array' => [new \ArrayObject(['foo' => 'bar'])],
            'iterator_array' => [new \ArrayIterator(['bar'])],

        ]);

        $graph->traverse($record, $visitor);

        $expect = <<<'JSON'
{
    "string": "foo",
    "stringobject": "foo",
    "integer": 1,
    "float": 0.5,
    "boolean": true,
    "date": "2016-02-10T19:00:00Z",
    "array": [
        "foo"
    ],
    "arrayassoc": {
        "foo": "bar"
    },
    "object": {
        "foo": "bar"
    },
    "record": {
        "foo": "bar"
    },
    "jsonobject": {
        "foo": "bar"
    },
    "arrayobject": {
        "foo": "bar"
    },
    "iterator": [
        "bar"
    ],
    "string_array": [
        "foo"
    ],
    "stringobject_array": [
        "foo"
    ],
    "integer_array": [
        1
    ],
    "float_array": [
        0.5
    ],
    "boolean_array": [
        true
    ],
    "date_array": [
        "2016-02-10T19:00:00Z"
    ],
    "null_array": {
        "0": ""
    },
    "array_array": [
        [
            "foo"
        ]
    ],
    "arrayassoc_array": [
        {
            "foo": "bar"
        }
    ],
    "object_array": [
        {
            "foo": "bar"
        }
    ],
    "record_array": [
        {
            "foo": "bar"
        }
    ],
    "jsonobject_array": [
        {
            "foo": "bar"
        }
    ],
    "arrayobject_array": [
        {
            "foo": "bar"
        }
    ],
    "iterator_array": [
        [
            "bar"
        ]
    ]
}
JSON;

        $actual = json_encode($visitor->getObject(), JSON_PRETTY_PRINT);

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
}

class JsonObject implements \JsonSerializable
{
    public function jsonSerialize()
    {
        return ['foo' => 'bar'];
    }
}

class StringObject
{
    public function __toString()
    {
        return 'foo';
    }
}
