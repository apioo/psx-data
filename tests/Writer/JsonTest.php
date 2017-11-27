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

namespace PSX\Data\Tests\Writer;

use PSX\Data\Tests\WriterTestCase;
use PSX\Data\Writer\Json;
use PSX\Http\MediaType;

/**
 * JsonTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonTest extends WriterTestCase
{
    public function testWriteRecord()
    {
        $writer = new Json();
        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
{
  "id":1,
  "author":"foo",
  "title":"bar",
  "content":"foobar",
  "date":"2012-03-11T13:37:21Z"
}
TEXT;

        $this->assertJsonStringEqualsJsonString($expect, $actual);
    }

    public function testWriteCollection()
    {
        $writer = new Json();
        $actual = $writer->write($this->getCollection());

        $expect = <<<TEXT
{
  "totalResults":2,
  "startIndex":0,
  "itemsPerPage":8,
  "entry":[{
    "id":1,
    "author":"foo",
    "title":"bar",
    "content":"foobar",
    "date":"2012-03-11T13:37:21Z"
  },{
    "id":2,
    "author":"foo",
    "title":"bar",
    "content":"foobar",
    "date":"2012-03-11T13:37:21Z"
  }]
}
TEXT;

        $this->assertJsonStringEqualsJsonString($expect, $actual);
    }

    public function testWriteComplex()
    {
        $writer = new Json();
        $actual = $writer->write($this->getComplex());

        $expect = <<<TEXT
{
  "published":"2011-02-10T15:04:55Z",
  "actor":{
    "displayName":"Martin Smith",
    "id":"tag:example.org,2011:martin",
    "objectType":"person",
    "url":"http:\/\/example.org\/martin"
  },
  "object":{
    "id":"tag:example.org,2011:abc123\/xyz",
    "url":"http:\/\/example.org\/blog\/2011\/02\/entry"
  },
  "target":{
    "displayName":"Martin's Blog",
    "id":"tag:example.org,2011:abc123",
    "objectType":"blog",
    "url":"http:\/\/example.org\/blog\/"
  },
  "verb":"post"
}
TEXT;

        $this->assertJsonStringEqualsJsonString($expect, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Json();
        $actual = $writer->write($this->getEmpty());

        $expect = <<<TEXT
{}
TEXT;

        $this->assertJsonStringEqualsJsonString($expect, $actual);
    }

    public function testWriteArray()
    {
        $writer = new Json();
        $actual = $writer->write($this->getArray());

        $expect = <<<TEXT
[
    {
        "id": 1,
        "author": "foo",
        "title": "bar",
        "content": "foobar",
        "date": "2012-03-11T13:37:21Z"
    },
    {
        "id": 2,
        "author": "foo",
        "title": "bar",
        "content": "foobar",
        "date": "2012-03-11T13:37:21Z"
    }
]
TEXT;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testWriteArrayScalar()
    {
        $writer = new Json();
        $actual = $writer->write($this->getArrayScalar());

        $expect = <<<TEXT
[
    "foo",
    "bar"
]
TEXT;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value must be an array or object
     */
    public function testWriteScalar()
    {
        $writer = new Json();
        $writer->write($this->getScalar());
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Json();

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/json')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
    }

    public function testGetContentType()
    {
        $writer = new Json();

        $this->assertEquals('application/json', $writer->getContentType());
    }
}
