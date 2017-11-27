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
use PSX\Data\Writer\Form;
use PSX\Http\MediaType;

/**
 * FormTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FormTest extends WriterTestCase
{
    public function testWriteRecord()
    {
        $writer = new Form();
        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
id=1&author=foo&title=bar&content=foobar&date=2012-03-11T13%3A37%3A21Z
TEXT;

        $this->assertEquals($expect, $actual);
    }

    public function testWriteCollection()
    {
        $writer = new Form();
        $actual = $writer->write($this->getCollection());

        $expect = <<<TEXT
totalResults=2&startIndex=0&itemsPerPage=8&entry%5B0%5D%5Bid%5D=1&entry%5B0%5D%5Bauthor%5D=foo&entry%5B0%5D%5Btitle%5D=bar&entry%5B0%5D%5Bcontent%5D=foobar&entry%5B0%5D%5Bdate%5D=2012-03-11T13%3A37%3A21Z&entry%5B1%5D%5Bid%5D=2&entry%5B1%5D%5Bauthor%5D=foo&entry%5B1%5D%5Btitle%5D=bar&entry%5B1%5D%5Bcontent%5D=foobar&entry%5B1%5D%5Bdate%5D=2012-03-11T13%3A37%3A21Z
TEXT;

        $this->assertEquals($expect, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Form();
        $actual = $writer->write($this->getEmpty());

        $expect = <<<TEXT
TEXT;

        $this->assertEquals($expect, $actual);
    }

    public function testWriteArray()
    {
        $writer = new Form();
        $actual = $writer->write($this->getArray());

        $expect = <<<TEXT
0%5Bid%5D=1&0%5Bauthor%5D=foo&0%5Btitle%5D=bar&0%5Bcontent%5D=foobar&0%5Bdate%5D=2012-03-11T13%3A37%3A21Z&1%5Bid%5D=2&1%5Bauthor%5D=foo&1%5Btitle%5D=bar&1%5Bcontent%5D=foobar&1%5Bdate%5D=2012-03-11T13%3A37%3A21Z
TEXT;

        $this->assertEquals($expect, $actual, $actual);
    }

    public function testWriteArrayScalar()
    {
        $writer = new Form();
        $actual = $writer->write($this->getArrayScalar());

        $expect = <<<TEXT
0=foo&1=bar
TEXT;

        $this->assertEquals($expect, $actual, $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value must be an array or object
     */
    public function testWriteScalar()
    {
        $writer = new Form();
        $writer->write($this->getScalar());
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Form();

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/x-www-form-urlencoded')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('application/xml')));
    }

    public function testGetContentType()
    {
        $writer = new Form();

        $this->assertEquals('application/x-www-form-urlencoded', $writer->getContentType());
    }
}
