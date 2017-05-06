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

use PSX\Data\Payload;
use PSX\Data\Tests\Processor\Model\Entry;
use PSX\Schema\Visitor\OutgoingVisitor;

/**
 * ProcessorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ProcessorTest extends ProcessorTestCase
{
    public function testRead()
    {
        $entry = $this->processor->read(Entry::class, Payload::json('{"title": "foo"}'));

        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals('foo', $entry->getTitle());
    }

    /**
     * @expectedException \PSX\Schema\ValidationException
     */
    public function testReadError()
    {
        $schema = $this->processor
            ->getConfiguration()
            ->getSchemaManager()
            ->getSchema(Entry::class);

        $this->processor->read($schema, Payload::json('{"title": "foo", "bar": "foo"}'));
    }

    public function testParse()
    {
        $data = $this->processor->parse(Payload::json('{"title": "foo"}'));

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals('foo', $data->title);
    }

    public function testWrite()
    {
        $entry = new Entry();
        $entry->setTitle('foo');

        $data = $this->processor->write(Payload::json($entry));

        $this->assertJsonStringEqualsJsonString('{"title": "foo"}', $data);
    }

    public function testTransform()
    {
        $entry = new Entry();
        $entry->setTitle('foo');

        $data = $this->processor->transform($entry);

        $this->assertInstanceOf('PSX\Record\Record', $data);
        $this->assertEquals('foo', $data->title);
    }
}
