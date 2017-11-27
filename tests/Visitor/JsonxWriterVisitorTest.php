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
use PSX\Data\Visitor\JsonxWriterVisitor;
use XMLWriter;

/**
 * JsonxWriterVisitorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonxWriterVisitorTest extends VisitorTestCase
{
    public function testTraverseObject()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getObject(), new JsonxWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedObject(), $writer->outputMemory());
    }

    public function testTraverseArray()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getArray(), new JsonxWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedArray(), $writer->outputMemory());
    }

    public function testTraverseArrayNested()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayNested(), new JsonxWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedArrayNested(), $writer->outputMemory());
    }

    public function testTraverseArrayScalar()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayScalar(), new JsonxWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpectedArrayScalar(), $writer->outputMemory());
    }

    public function testTraverseNullValue()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $data = new \stdClass();
        $data->foo = 'bar';
        $data->bar = null;

        $graph = new GraphTraverser();
        $graph->traverse($data, new JsonxWriterVisitor($writer));

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
 <json:string name="foo">bar</json:string>
 <json:null name="bar" />
</json:object>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $writer->outputMemory());
    }

    protected function getExpectedObject()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
 <json:number name="id">1</json:number>
 <json:string name="title">foobar</json:string>
 <json:boolean name="active">true</json:boolean>
 <json:boolean name="disabled">false</json:boolean>
 <json:number name="rating">12.45</json:number>
 <json:null name="age"/>
 <json:string name="date">2014-01-01T12:34:47+01:00</json:string>
 <json:string name="href">http://foo.com</json:string>
 <json:object name="person">
  <json:string name="title">Foo</json:string>
 </json:object>
 <json:object name="category">
  <json:object name="general">
   <json:object name="news">
    <json:string name="technic">Foo</json:string>
   </json:object>
  </json:object>
 </json:object>
 <json:array name="tags">
  <json:string>bar</json:string>
  <json:string>foo</json:string>
  <json:string>test</json:string>
 </json:array>
 <json:array name="entry">
  <json:object>
   <json:string name="title">bar</json:string>
  </json:object>
  <json:object>
   <json:string name="title">foo</json:string>
  </json:object>
 </json:array>
</json:object>
XML;
    }

    protected function getExpectedArray()
    {
        return <<<XML
<?xml version="1.0"?>
<json:array xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:object>
    <json:number name="id">1</json:number>
    <json:string name="title">foobar</json:string>
    <json:boolean name="active">true</json:boolean>
    <json:boolean name="disabled">false</json:boolean>
    <json:number name="rating">12.45</json:number>
  </json:object>
  <json:object>
    <json:number name="id">2</json:number>
    <json:string name="title">foo</json:string>
    <json:boolean name="active">false</json:boolean>
    <json:boolean name="disabled">false</json:boolean>
    <json:number name="rating">12.45</json:number>
  </json:object>
</json:array>
XML;
    }

    protected function getExpectedArrayNested()
    {
        return <<<XML
<?xml version="1.0"?>
<json:array xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:array>
    <json:string>foo</json:string>
    <json:string>bar</json:string>
  </json:array>
</json:array>
XML;
    }

    protected function getExpectedArrayScalar()
    {
        return <<<XML
<?xml version="1.0"?>
<json:array xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:string>foo</json:string>
  <json:string>bar</json:string>
</json:array>
XML;
    }
}
