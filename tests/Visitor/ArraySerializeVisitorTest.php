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
use PSX\Data\Visitor\ArraySerializeVisitor;

/**
 * ArraySerializeVisitorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ArraySerializeVisitorTest extends VisitorTestCase
{
    public function testTraverseObject()
    {
        $visitor = new ArraySerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getObject(), $visitor);

        $this->assertEquals($this->getExpectedObject(), $visitor->getObject());
    }

    public function testTraverseArray()
    {
        $visitor = new ArraySerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArray(), $visitor);

        $this->assertEquals($this->getExpectedArray(), $visitor->getArray());
    }

    public function testTraverseArrayNested()
    {
        $visitor = new ArraySerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayNested(), $visitor);

        $this->assertEquals($this->getExpectedArrayNested(), $visitor->getArray());
    }

    public function testTraverseArrayScalar()
    {
        $visitor = new ArraySerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayScalar(), $visitor);

        $this->assertEquals($this->getExpectedArrayScalar(), $visitor->getArray());
    }

    protected function getExpectedObject()
    {
        $person = array();
        $person['title'] = 'Foo';

        $category = array();
        $category['general'] = array();
        $category['general']['news'] = array();
        $category['general']['news']['technic'] = 'Foo';

        $entry = array();
        $entry[0] = array();
        $entry[0]['title'] = 'bar';
        $entry[1] = array();
        $entry[1]['title'] = 'foo';

        $record = array();
        $record['id'] = 1;
        $record['title'] = 'foobar';
        $record['active'] = true;
        $record['disabled'] = false;
        $record['rating'] = 12.45;
        $record['age'] = null;
        $record['date'] = '2014-01-01T12:34:47+01:00';
        $record['href'] = 'http://foo.com';
        $record['person'] = $person;
        $record['category'] = $category;
        $record['tags'] = array('bar', 'foo', 'test');
        $record['entry'] = $entry;

        return $record;
    }

    protected function getExpectedArray()
    {
        $record1['id'] = 1;
        $record1['title'] = 'foobar';
        $record1['active'] = true;
        $record1['disabled'] = false;
        $record1['rating'] = 12.45;

        $record2['id'] = 2;
        $record2['title'] = 'foo';
        $record2['active'] = false;
        $record2['disabled'] = false;
        $record2['rating'] = 12.45;

        return [$record1, $record2];
    }

    protected function getExpectedArrayNested()
    {
        return [
            ['foo', 'bar']
        ];
    }

    protected function getExpectedArrayScalar()
    {
        return ['foo', 'bar'];
    }
}
