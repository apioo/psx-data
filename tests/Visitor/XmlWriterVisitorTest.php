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
use PSX\Data\Visitor\XmlWriterVisitor;
use PSX\Record\Record;
use XMLWriter;

/**
 * XmlWriterVisitor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlWriterVisitorTest extends VisitorTestCase
{
    public function testTraverseObject()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getObject(), new XmlWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedObject(), $writer->outputMemory());
    }

    public function testTraverseArray()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getArray(), new XmlWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedArray(), $writer->outputMemory());
    }

    public function testTraverseArrayNested()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayNested(), new XmlWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedArrayNested(), $writer->outputMemory());
    }

    public function testTraverseArrayScalar()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayScalar(), new XmlWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedArrayScalar(), $writer->outputMemory());
    }

    /**
     * A XML element name can only contain alnum and _. All other characters are
     * replaced with an _
     */
    public function testInvalidElementName()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $key = '';
        for ($i = 0; $i <= 0x7F; $i++) {
            $key.= chr($i);
        }

        $record = Record::fromArray([
          $key => 'foo'
        ]);

        $graph = new GraphTraverser();
        $graph->traverse($record, new XmlWriterVisitor($writer));

        $expect = <<<XML
<?xml version="1.0"?>
<record type="object">
  <________________________________________________0123456789_______ABCDEFGHIJKLMNOPQRSTUVWXYZ______abcdefghijklmnopqrstuvwxyz_____ type="string">foo</________________________________________________0123456789_______ABCDEFGHIJKLMNOPQRSTUVWXYZ______abcdefghijklmnopqrstuvwxyz_____>
</record>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $writer->outputMemory());
    }

    /**
     * A XML element name can not start with an number
     */
    public function testInvalidElementNameNumberAtStart()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $record = Record::fromArray([
          '09foo' => 'foo'
        ]);

        $graph = new GraphTraverser();
        $graph->traverse($record, new XmlWriterVisitor($writer));

        $expect = <<<XML
<?xml version="1.0"?>
<record type="object">
  <_09foo type="string">foo</_09foo>
</record>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $writer->outputMemory());
    }

    protected function getExpectedObject()
    {
        return <<<XML
<?xml version="1.0"?>
<record type="object">
  <id type="integer">1</id>
  <title type="string">foobar</title>
  <active type="boolean">true</active>
  <disabled type="boolean">false</disabled>
  <rating type="float">12.45</rating>
  <age type="null"/>
  <date type="date-time">2014-01-01T12:34:47+01:00</date>
  <href type="uri">http://foo.com</href>
  <person type="object">
    <title type="string">Foo</title>
  </person>
  <category type="object">
    <general type="object">
      <news type="object">
        <technic type="string">Foo</technic>
      </news>
    </general>
  </category>
  <tags type="array">
    <entry type="string">bar</entry>
    <entry type="string">foo</entry>
    <entry type="string">test</entry>
  </tags>
  <entry type="array">
    <entry type="object">
      <title type="string">bar</title>
    </entry>
    <entry type="object">
      <title type="string">foo</title>
    </entry>
  </entry>
</record>
XML;
    }

    protected function getExpectedArray()
    {
        return <<<XML
<?xml version="1.0"?>
<collection type="array">
  <entry type="object">
    <id type="integer">1</id>
    <title type="string">foobar</title>
    <active type="boolean">true</active>
    <disabled type="boolean">false</disabled>
    <rating type="float">12.45</rating>
  </entry>
  <entry type="object">
    <id type="integer">2</id>
    <title type="string">foo</title>
    <active type="boolean">false</active>
    <disabled type="boolean">false</disabled>
    <rating type="float">12.45</rating>
  </entry>
</collection>
XML;
    }

    protected function getExpectedArrayNested()
    {
        return <<<XML
<?xml version="1.0"?>
<collection type="array">
  <entry type="array">
    <entry type="string">foo</entry>
    <entry type="string">bar</entry>
  </entry>
</collection>
XML;
    }

    protected function getExpectedArrayScalar()
    {
        return <<<XML
<?xml version="1.0"?>
<collection type="array">
  <entry type="string">foo</entry>
  <entry type="string">bar</entry>
</collection>
XML;
    }
}
