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

use PSX\Data\Accessor;
use PSX\Record\Record;
use PSX\Validate\Filter;

/**
 * AccessorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideSources
     */
    public function testGet($source)
    {
        $this->assertEquals('bar', Accessor::get($source, '/foo'));
        $this->assertEquals(1, Accessor::get($source, '/bar/foo'));
        $this->assertEquals('bar', Accessor::get($source, '/tes/0/foo'));
    }

    public function testGetMissing()
    {
        $this->assertNull(Accessor::get(['foo' => 'bar'], 'bar')); 
    }

    /**
     * @dataProvider provideSources
     */
    public function testGetFilter($source)
    {
        $this->assertEquals('bar', Accessor::get($source, '/foo'));
        $this->assertEquals('1', Accessor::get($source, '/bar/foo'));
        $this->assertEquals('bar', Accessor::get($source, '/tes/0/foo', [new Filter\Length(3, 8)]));
    }

    public function provideSources()
    {
        $sources = [];

        // array
        $source = [
            'foo' => 'bar',
            'bar' => [
                'foo' => '1',
            ],
            'tes' => [
                [
                    'foo' => 'bar'
                ],
            ],
        ];

        $sources[] = [$source];

        // stdClass
        $source = new \stdClass();
        $source->foo = 'bar';
        $source->bar = new \stdClass();
        $source->bar->foo = 1;
        $source->tes = [];
        $source->tes[0] = new \stdClass();
        $source->tes[0]->foo = 'bar';

        $sources[] = [$source];

        // RecordInterface
        $source = Record::fromArray([
            'foo' => 'bar',
            'bar' => Record::fromArray([
                'foo' => '1'
            ]),
            'tes' => [
                Record::fromArray([
                    'foo' => 'bar'
                ])
            ]
        ]);

        $sources[] = [$source];

        return $sources;
    }

    public function testGetUnknownKey()
    {
        $source = [
            'bar' => [
                'foo' => '1',
            ],
        ];

        $this->assertNull(Accessor::get($source, '/bar/bar'));
    }

    public function testGetUnknownKeyInvalidSource()
    {
        $source = 'foo';

        $this->assertNull(Accessor::get($source, '/bar/bar'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage /foo has an invalid length min 4 and max 8 signs
     */
    public function testFilterInvalid()
    {
        Accessor::get(['foo' => 'bar'], '/foo', [new Filter\Length(4, 8)]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage /foo contains an invalid value
     */
    public function testFilterInvalidClosure()
    {
        Accessor::get(['foo' => 'bar'], '/foo', [function(){
            return false;
        }]);
    }
}
