<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * @link    https://phpsx.org
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

    protected function getExpectedObject(): Record
    {
        $person = new Record();
        $person->put('title', 'Foo');

        /** @var Record<Record<Record<string>>> $category */
        $category = new Record();
        $category->put('general', new Record());
        $category->get('general')->put('news', new Record());
        $category->get('general')->get('news')->put('technic', 'Foo');

        $entry = array();
        $entry[0] = new Record();
        $entry[0]->put('title', 'bar');
        $entry[1] = new Record();
        $entry[1]->put('title', 'foo');

        $record = new Record();
        $record->put('id', 1);
        $record->put('title', 'foobar');
        $record->put('active', true);
        $record->put('disabled', false);
        $record->put('rating', 12.45);
        $record->put('age', null);
        $record->put('date', '2014-01-01T12:34:47Z');
        $record->put('href', 'http://foo.com');
        $record->put('person', $person);
        $record->put('category', $category);
        $record->put('tags', ['bar', 'foo', 'test']);
        $record->put('entry', $entry);

        return $record;
    }

    protected function getExpectedArray(): array
    {
        $record1 = new Record();
        $record1->put('id', 1);
        $record1->put('title', 'foobar');
        $record1->put('active', true);
        $record1->put('disabled', false);
        $record1->put('rating', 12.45);

        $record2 = new Record();
        $record2->put('id', 2);
        $record2->put('title', 'foo');
        $record2->put('active', false);
        $record2->put('disabled', false);
        $record2->put('rating', 12.45);

        return [$record1, $record2];
    }

    protected function getExpectedArrayNested(): array
    {
        return [
            ['foo', 'bar']
        ];
    }

    protected function getExpectedArrayScalar(): array
    {
        return ['foo', 'bar'];
    }
}
