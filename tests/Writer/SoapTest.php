<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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
use PSX\Data\Writer\Soap;
use PSX\Http\MediaType;
use PSX\Model\Error;

/**
 * SoapTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class SoapTest extends WriterTestCase
{
    public function testWriteRecord()
    {
        $writer = new Soap();
        $writer->setRequestMethod('GET');

        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
      <json:number name="id">1</json:number>
      <json:string name="author">foo</json:string>
      <json:string name="title">bar</json:string>
      <json:string name="content">foobar</json:string>
      <json:string name="date">2012-03-11T13:37:21Z</json:string>
    </json:object>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
        $this->assertEquals('get', $writer->getRequestMethod());
    }

    public function testWriteCollection()
    {
        $writer = new Soap();
        $writer->setRequestMethod('GET');

        $actual = $writer->write($this->getCollection());

        $expect = <<<TEXT
<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
      <json:number name="totalResults">2</json:number>
      <json:number name="startIndex">0</json:number>
      <json:number name="itemsPerPage">8</json:number>
      <json:array name="entry">
        <json:object>
          <json:number name="id">1</json:number>
          <json:string name="author">foo</json:string>
          <json:string name="title">bar</json:string>
          <json:string name="content">foobar</json:string>
          <json:string name="date">2012-03-11T13:37:21Z</json:string>
        </json:object>
        <json:object>
          <json:number name="id">2</json:number>
          <json:string name="author">foo</json:string>
          <json:string name="title">bar</json:string>
          <json:string name="content">foobar</json:string>
          <json:string name="date">2012-03-11T13:37:21Z</json:string>
        </json:object>
      </json:array>
    </json:object>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
        $this->assertEquals('get', $writer->getRequestMethod());
    }

    public function testWriteComplex()
    {
        $writer = new Soap();
        $writer->setRequestMethod('GET');

        $actual = $writer->write($this->getComplex());

        $expect = <<<TEXT
<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
      <json:string name="verb">post</json:string>
      <json:object name="actor">
        <json:string name="id">tag:example.org,2011:martin</json:string>
        <json:string name="objectType">person</json:string>
        <json:string name="displayName">Martin Smith</json:string>
        <json:string name="url">http://example.org/martin</json:string>
      </json:object>
      <json:object name="object">
        <json:string name="id">tag:example.org,2011:abc123/xyz</json:string>
        <json:string name="url">http://example.org/blog/2011/02/entry</json:string>
      </json:object>
      <json:object name="target">
        <json:string name="id">tag:example.org,2011:abc123</json:string>
        <json:string name="objectType">blog</json:string>
        <json:string name="displayName">Martin's Blog</json:string>
        <json:string name="url">http://example.org/blog/</json:string>
      </json:object>
      <json:string name="published">2011-02-10T15:04:55Z</json:string>
    </json:object>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Soap();
        $actual = $writer->write($this->getEmpty());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx"/>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteArray()
    {
        $writer = new Soap();
        $actual = $writer->write($this->getArray());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <json:array xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
   <json:object>
    <json:number name="id">1</json:number>
    <json:string name="author">foo</json:string>
    <json:string name="title">bar</json:string>
    <json:string name="content">foobar</json:string>
    <json:string name="date">2012-03-11T13:37:21Z</json:string>
   </json:object>
   <json:object>
    <json:number name="id">2</json:number>
    <json:string name="author">foo</json:string>
    <json:string name="title">bar</json:string>
    <json:string name="content">foobar</json:string>
    <json:string name="date">2012-03-11T13:37:21Z</json:string>
   </json:object>
  </json:array>
 </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteArrayScalar()
    {
        $writer = new Soap();
        $actual = $writer->write($this->getArrayScalar());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <json:array xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
   <json:string>foo</json:string>
   <json:string>bar</json:string>
  </json:array>
 </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteScalar()
    {
        $writer = new Soap();
        $actual = $writer->write($this->getScalar());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>foobar</soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Soap();

        $this->assertTrue($writer->isContentTypeSupported(MediaType::parse('application/soap+xml')));
        $this->assertFalse($writer->isContentTypeSupported(MediaType::parse('text/html')));
    }

    public function testGetContentType()
    {
        $writer = new Soap();

        $this->assertEquals('text/xml', $writer->getContentType());
    }

    public function testWriteExceptionRecord()
    {
        $record = new Error();
        $record->setSuccess(false);
        $record->setTitle('An error occured');
        $record->setMessage('Foobar');
        $record->setTrace('Foo');
        $record->setContext('Bar');

        $writer = new Soap();
        $writer->setRequestMethod('GET');

        $actual = $writer->write($record);

        $expect = <<<TEXT
<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <soap:Fault>
      <faultcode>soap:Server</faultcode>
      <faultstring>Foobar</faultstring>
      <detail>
        <json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
          <json:boolean name="success">false</json:boolean>
          <json:string name="title">An error occured</json:string>
          <json:string name="message">Foobar</json:string>
          <json:string name="trace">Foo</json:string>
          <json:string name="context">Bar</json:string>
        </json:object>
      </detail>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }
}
