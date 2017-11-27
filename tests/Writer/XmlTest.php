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
use PSX\Data\Writer\Xml;
use PSX\Http\MediaType;

/**
 * XmlTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlTest extends WriterTestCase
{
    public function testWriteRecord()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
<?xml version="1.0"?>
<record type="object">
  <id type="integer">1</id>
  <author type="string">foo</author>
  <title type="string">bar</title>
  <content type="string">foobar</content>
  <date type="date-time">2012-03-11T13:37:21Z</date>
</record>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteCollection()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getCollection());

        $expect = <<<TEXT
<?xml version="1.0"?>
<collection type="object">
  <totalResults type="integer">2</totalResults>
  <startIndex type="integer">0</startIndex>
  <itemsPerPage type="integer">8</itemsPerPage>
  <entry type="array">
    <entry type="object">
      <id type="integer">1</id>
      <author type="string">foo</author>
      <title type="string">bar</title>
      <content type="string">foobar</content>
      <date type="date-time">2012-03-11T13:37:21Z</date>
    </entry>
    <entry type="object">
      <id type="integer">2</id>
      <author type="string">foo</author>
      <title type="string">bar</title>
      <content type="string">foobar</content>
      <date type="date-time">2012-03-11T13:37:21Z</date>
    </entry>
  </entry>
</collection>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteComplex()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getComplex());

        $expect = <<<TEXT
<?xml version="1.0"?>
<activity type="object">
  <verb type="string">post</verb>
  <actor type="object">
    <id type="string">tag:example.org,2011:martin</id>
    <objectType type="string">person</objectType>
    <displayName type="string">Martin Smith</displayName>
    <url type="string">http://example.org/martin</url>
  </actor>
  <object type="object">
    <id type="string">tag:example.org,2011:abc123/xyz</id>
    <url type="string">http://example.org/blog/2011/02/entry</url>
  </object>
  <target type="object">
    <id type="string">tag:example.org,2011:abc123</id>
    <objectType type="string">blog</objectType>
    <displayName type="string">Martin's Blog</displayName>
    <url type="string">http://example.org/blog/</url>
  </target>
  <published type="date-time">2011-02-10T15:04:55Z</published>
</activity>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getEmpty());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<record type="object"/>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteArray()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getArray());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<collection type="array">
 <entry type="object">
  <id type="integer">1</id>
  <author type="string">foo</author>
  <title type="string">bar</title>
  <content type="string">foobar</content>
  <date type="date-time">2012-03-11T13:37:21Z</date>
 </entry>
 <entry type="object">
  <id type="integer">2</id>
  <author type="string">foo</author>
  <title type="string">bar</title>
  <content type="string">foobar</content>
  <date type="date-time">2012-03-11T13:37:21Z</date>
 </entry>
</collection>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteArrayScalar()
    {
        $writer = new Xml();
        $actual = $writer->write($this->getArrayScalar());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<collection type="array">
 <entry type="string">foo</entry>
 <entry type="string">bar</entry>
</collection>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Value must be an array or object
     */
    public function testWriteScalar()
    {
        $writer = new Xml();
        $writer->write($this->getScalar());
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Xml();

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/xml')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
    }

    public function testGetContentType()
    {
        $writer = new Xml();

        $this->assertEquals('application/xml', $writer->getContentType());
    }
}
