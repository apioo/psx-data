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
use PSX\Data\Visitor\RecordSerializeVisitor;
use PSX\Record\Record;

/**
 * RecordSerializeVisitorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordSerializeVisitorTest extends VisitorTestCase
{
    public function testTraverseObject()
    {
        $visitor = new RecordSerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getObject(), $visitor);

        $this->assertEquals($this->getExpectedObject(), $visitor->getObject());
    }

    public function testTraverseArray()
    {
        $visitor = new RecordSerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArray(), $visitor);

        $this->assertEquals($this->getExpectedArray(), $visitor->getArray());
    }

    public function testTraverseArrayNested()
    {
        $visitor = new RecordSerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayNested(), $visitor);

        $this->assertEquals($this->getExpectedArrayNested(), $visitor->getArray());
    }

    public function testTraverseArrayScalar()
    {
        $visitor = new RecordSerializeVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayScalar(), $visitor);

        $this->assertEquals($this->getExpectedArrayScalar(), $visitor->getArray());
    }

    protected function getExpectedObject()
    {
        $person = new Record();
        $person->setProperty('title', 'Foo');

        $category = new Record();
        $category->setProperty('general', new Record());
        $category['general']->setProperty('news', new Record());
        $category['general']['news']->setProperty('technic', 'Foo');

        $entry = array();
        $entry[0] = new Record();
        $entry[0]->setProperty('title', 'bar');
        $entry[1] = new Record();
        $entry[1]->setProperty('title', 'foo');

        $record = new Record();
        $record->setProperty('id', 1);
        $record->setProperty('title', 'foobar');
        $record->setProperty('active', true);
        $record->setProperty('disabled', false);
        $record->setProperty('rating', 12.45);
        $record->setProperty('age', null);
        $record->setProperty('date', '2014-01-01T12:34:47+01:00');
        $record->setProperty('href', 'http://foo.com');
        $record->setProperty('person', $person);
        $record->setProperty('category', $category);
        $record->setProperty('tags', ['bar', 'foo', 'test']);
        $record->setProperty('entry', $entry);

        return $record;
    }

    protected function getExpectedArray()
    {
        $record1 = new Record();
        $record1->setProperty('id', 1);
        $record1->setProperty('title', 'foobar');
        $record1->setProperty('active', true);
        $record1->setProperty('disabled', false);
        $record1->setProperty('rating', 12.45);

        $record2 = new Record();
        $record2->setProperty('id', 2);
        $record2->setProperty('title', 'foo');
        $record2->setProperty('active', false);
        $record2->setProperty('disabled', false);
        $record2->setProperty('rating', 12.45);

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
