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

use PSX\Data\Reader;
use PSX\Data\ReaderFactory;

/**
 * ReaderFactoryTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ReaderFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PSX\Data\ReaderFactory
     */
    protected $readerFactory;

    public function setUp()
    {
        $this->readerFactory = new ReaderFactory();
        $this->readerFactory->addReader(new Reader\Json());
        $this->readerFactory->addReader(new Reader\Form());
        $this->readerFactory->addReader(new Reader\Xml());
    }

    public function testGetDefaultReader()
    {
        $this->assertInstanceOf(Reader\Json::class, $this->readerFactory->getDefaultReader());
    }

    public function testGetReaderByContentType()
    {
        $this->assertInstanceOf(Reader\Json::class, $this->readerFactory->getReaderByContentType('application/json'));
        $this->assertInstanceOf(Reader\Form::class, $this->readerFactory->getReaderByContentType('application/x-www-form-urlencoded'));
        $this->assertInstanceOf(Reader\Xml::class, $this->readerFactory->getReaderByContentType('application/xml'));
        $this->assertNull($this->readerFactory->getReaderByContentType('application/foo'));
    }

    public function testGetReaderByContentTypeSupportedReader()
    {
        $supportedReader = array(Reader\Form::class, Reader\Xml::class);
        $contentType     = 'application/xml';

        $this->assertInstanceOf(Reader\Xml::class, $this->readerFactory->getReaderByContentType($contentType, $supportedReader));
    }

    public function testGetReaderByInstance()
    {
        $this->assertInstanceOf(Reader\Json::class, $this->readerFactory->getReaderByInstance(Reader\Json::class));
        $this->assertInstanceOf(Reader\Form::class, $this->readerFactory->getReaderByInstance(Reader\Form::class));
        $this->assertInstanceOf(Reader\Xml::class, $this->readerFactory->getReaderByInstance(Reader\Xml::class));
        $this->assertEquals(null, $this->readerFactory->getReaderByInstance('PSX\Data\Reader\Foo'));
    }
}
