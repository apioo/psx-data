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

use PSX\Data\Writer;
use PSX\Data\WriterFactory;
use PSX\Data\WriterInterface;

/**
 * WriterFactoryTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WriterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PSX\Data\WriterFactory
     */
    protected $writerFactory;

    public function setUp()
    {
        $this->writerFactory = new WriterFactory();
        $this->writerFactory->addWriter(new Writer\Json(), 48);
        $this->writerFactory->addWriter(new Writer\Atom(), 32);
        $this->writerFactory->addWriter(new Writer\Form(), 24);
        $this->writerFactory->addWriter(new Writer\Jsonp(), 16);
        $this->writerFactory->addWriter(new Writer\Soap('http://phpsx.org/2014/data'), 8);
        $this->writerFactory->addWriter(new Writer\Xml(), 0);
    }

    public function testGetDefaultWriter()
    {
        $this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getDefaultWriter());
    }

    public function testGetDefaultWriterEmpty()
    {
        $writerFactory = new WriterFactory();

        $this->assertNull($writerFactory->getDefaultWriter());
    }

    public function testGetWriterByContentType()
    {
        $this->assertInstanceOf(Writer\Json::class, $this->writerFactory->getWriterByContentType('application/json'));
        $this->assertInstanceOf(Writer\Atom::class, $this->writerFactory->getWriterByContentType('application/atom+xml'));
        $this->assertInstanceOf(Writer\Form::class, $this->writerFactory->getWriterByContentType('application/x-www-form-urlencoded'));
        $this->assertInstanceOf(Writer\Jsonp::class, $this->writerFactory->getWriterByContentType('application/javascript'));
        $this->assertInstanceOf(Writer\Soap::class, $this->writerFactory->getWriterByContentType('application/soap+xml'));
        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('application/xml'));
        $this->assertNull($this->writerFactory->getWriterByContentType('application/foo'));
        $this->assertNull($this->writerFactory->getWriterByContentType('application/json', array(Writer\Xml::class)));
    }

    public function testGetWriterByContentTypeSupportedWriter()
    {
        $contentType = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType($contentType, array(Writer\Xml::class)));
        $this->assertNull($this->writerFactory->getWriterByContentType($contentType, array(Writer\Json::class)));
    }

    public function testGetWriterByContentTypeOrder()
    {
        $supportedWriter = array(Writer\Xml::class);
        $contentType     = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType($contentType, $supportedWriter));
    }

    public function testGetWriterByInstance()
    {
        $this->assertInstanceOf(Writer\Json::class, $this->writerFactory->getWriterByInstance(Writer\Json::class));
        $this->assertInstanceOf(Writer\Atom::class, $this->writerFactory->getWriterByInstance(Writer\Atom::class));
        $this->assertInstanceOf(Writer\Form::class, $this->writerFactory->getWriterByInstance(Writer\Form::class));
        $this->assertInstanceOf(Writer\Jsonp::class, $this->writerFactory->getWriterByInstance(Writer\Jsonp::class));
        $this->assertInstanceOf(Writer\Soap::class, $this->writerFactory->getWriterByInstance(Writer\Soap::class));
        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByInstance(Writer\Xml::class));
        $this->assertNull($this->writerFactory->getWriterByInstance('PSX\Data\Writer\Foo'));
    }

    public function testGetWriterClassNameByFormat()
    {
        $this->assertEquals(Writer\Json::class, $this->writerFactory->getWriterClassNameByFormat('json'));
        $this->assertEquals(Writer\Atom::class, $this->writerFactory->getWriterClassNameByFormat('atom'));
        $this->assertEquals(Writer\Form::class, $this->writerFactory->getWriterClassNameByFormat('form'));
        $this->assertEquals(Writer\Jsonp::class, $this->writerFactory->getWriterClassNameByFormat('jsonp'));
        $this->assertEquals(Writer\Soap::class, $this->writerFactory->getWriterClassNameByFormat('soap'));
        $this->assertEquals(Writer\Xml::class, $this->writerFactory->getWriterClassNameByFormat('xml'));
        $this->assertNull($this->writerFactory->getWriterClassNameByFormat('foo'));
        $this->assertNull($this->writerFactory->getWriterClassNameByFormat(''));
    }

    public function testContentNegotiationExplicit()
    {
        $this->writerFactory->setContentNegotiation('text/plain', WriterInterface::XML);

        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('text/plain'));
    }

    public function testContentNegotiationWildcardSubtype()
    {
        $this->writerFactory->setContentNegotiation('text/*', WriterInterface::XML);

        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('text/plain'));
        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('text/foo'));
        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('application/xml'));
        $this->assertInstanceOf(Writer\Json::class, $this->writerFactory->getWriterByContentType('application/json'));
        $this->assertNull($this->writerFactory->getWriterByContentType('image/png'));
    }

    public function testContentNegotiationAll()
    {
        $this->writerFactory->setContentNegotiation('*/*', WriterInterface::XML);

        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('text/plain'));
        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('application/json'));
    }

    public function testContentNegotiation()
    {
        $this->writerFactory->setContentNegotiation('image/*', WriterInterface::JSON);

        $this->assertInstanceOf(Writer\Json::class, $this->writerFactory->getWriterByContentType('image/webp,*/*;q=0.8'));
        $this->assertInstanceOf(Writer\Json::class, $this->writerFactory->getWriterByContentType('image/webp'));
        $this->assertNull($this->writerFactory->getWriterByContentType('text/plain'));
        $this->assertInstanceOf(Writer\Json::class, $this->writerFactory->getWriterByContentType('image/png, image/svg+xml, image/*;q=0.8, */*;q=0.5'));
        $this->assertInstanceOf(Writer\Json::class, $this->writerFactory->getWriterByContentType('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'));
        $this->assertInstanceOf(Writer\Xml::class, $this->writerFactory->getWriterByContentType('text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'));
    }

    /**
     * Check whether we serve known browsers with html
     *
     * @dataProvider browserAcceptHeaderProvider
     */
    public function testBrowserAcceptHeaders($contentType, $className)
    {
        $this->assertInstanceOf($className, $this->writerFactory->getWriterByContentType($contentType));
    }

    public function browserAcceptHeaderProvider()
    {
        return [
            ['text/html, application/xhtml+xml, */*', Writer\Xml::class], // IE Version 11.0
            ['text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', Writer\Xml::class], // Chrome Version 43.0
            ['text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8', Writer\Xml::class], // Firefox Version 40.0.3
        ];
    }
}
