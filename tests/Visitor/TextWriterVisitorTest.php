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

namespace PSX\Data\Tests\Visitor;

use PSX\Data\GraphTraverser;
use PSX\Data\Visitor\TextWriterVisitor;
use PSX\Record\Record;

/**
 * TextWriterVisitorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TextWriterVisitorTest extends VisitorTestCase
{
    public function testTraverseObject()
    {
        $visitor = new TextWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getObject(), $visitor);

        $this->assertEquals($this->getExpectedObject(), $visitor->getOutput());
    }

    public function testTraverseArray()
    {
        $visitor = new TextWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArray(), $visitor);

        $this->assertEquals($this->getExpectedArray(), $visitor->getOutput());
    }

    public function testTraverseArrayNested()
    {
        $visitor = new TextWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayNested(), $visitor);

        $this->assertEquals($this->getExpectedArrayNested(), $visitor->getOutput());
    }

    public function testTraverseArrayScalar()
    {
        $visitor = new TextWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayScalar(), $visitor);

        $this->assertEquals($this->getExpectedArrayScalar(), $visitor->getOutput());
    }

    public function testTraverseTextLong()
    {
        $visitor = new TextWriterVisitor();
        $record  = new Record('foo', array(
            'title' => 'Lorem ipsum dolor' . "\n" . 'sit amet, consetetur sadipscin'
        ));

        $graph = new GraphTraverser();
        $graph->traverse($record, $visitor);

        $except = <<<TEXT
Object(foo){
    title = Lorem ipsum dolor sit amet, cons (...)
}

TEXT;

        $this->assertEquals($except, $visitor->getOutput());
    }

    protected function getExpectedObject()
    {
        return <<<TEXT
Object(record){
    id = 1
    title = foobar
    active = true
    disabled = false
    rating = 12.45
    age = null
    date = 2014-01-01T12:34:47+01:00
    href = http://foo.com
    person = Object(person){
        title = Foo
    }
    category = Object(category){
        general = Object(category){
            news = Object(category){
                technic = Foo
            }
        }
    }
    tags = Array[
        bar
        foo
        test
    ]
    entry = Array[
        Object(entry){
            title = bar
        }
        Object(entry){
            title = foo
        }
    ]
}

TEXT;
    }

    protected function getExpectedArray()
    {
        return <<<TEXT
Array[
    Object(record){
        id = 1
        title = foobar
        active = true
        disabled = false
        rating = 12.45
    }
    Object(record){
        id = 2
        title = foo
        active = false
        disabled = false
        rating = 12.45
    }
]

TEXT;
    }

    protected function getExpectedArrayNested()
    {
        return <<<TEXT
Array[
    Array[
        foo
        bar
    ]
]

TEXT;
    }

    protected function getExpectedArrayScalar()
    {
        return <<<TEXT
Array[
    foo
    bar
]

TEXT;
    }
}
