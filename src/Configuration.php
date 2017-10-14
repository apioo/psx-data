<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Data;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use PSX\Schema\SchemaManagerInterface;

/**
 * Configuration
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Configuration
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    /**
     * @var \PSX\Schema\SchemaManagerInterface
     */
    protected $schemaManager;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var \PSX\Data\ReaderFactory
     */
    protected $readerFactory;

    /**
     * @var \PSX\Data\WriterFactory
     */
    protected $writerFactory;

    public function __construct(AnnotationReader $reader, SchemaManagerInterface $schemaManager, $namespace, ReaderFactory $readerFactory, WriterFactory $writerFactory)
    {
        $this->annotationReader = $reader;
        $this->schemaManager    = $schemaManager;
        $this->namespace        = $namespace;
        $this->readerFactory    = $readerFactory;
        $this->writerFactory    = $writerFactory;
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }

    /**
     * @return \PSX\Schema\SchemaManagerInterface
     */
    public function getSchemaManager()
    {
        return $this->schemaManager;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param \PSX\Data\ReaderFactory $readerFactory
     */
    public function setReaderFactory(ReaderFactory $readerFactory)
    {
        $this->readerFactory = $readerFactory;
    }

    /**
     * @return \PSX\Data\ReaderFactory
     */
    public function getReaderFactory()
    {
        return $this->readerFactory;
    }

    /**
     * @param \PSX\Data\WriterFactory $writerFactory
     */
    public function setWriterFactory(WriterFactory $writerFactory)
    {
        $this->writerFactory = $writerFactory;
    }

    /**
     * @return \PSX\Data\WriterFactory
     */
    public function getWriterFactory()
    {
        return $this->writerFactory;
    }

    public static function createDefault(AnnotationReader $reader, SchemaManagerInterface $schemaManager, $namespace = null)
    {
        return new self(
            $reader, 
            $schemaManager, 
            $namespace,
            self::createDefaultReaderFactory(),
            self::createDefaultWriterFactory($namespace)
        );
    }

    protected static function createDefaultReaderFactory()
    {
        $readerFactory = new ReaderFactory();
        $readerFactory->addReader(new Reader\Json(), 16);
        $readerFactory->addReader(new Reader\Form(), 8);
        $readerFactory->addReader(new Reader\Multipart(), 1);
        $readerFactory->addReader(new Reader\Xml(), 0);

        return $readerFactory;
    }

    protected static function createDefaultWriterFactory($soapNamespace = null)
    {
        $writerFactory = new WriterFactory();
        $writerFactory->addWriter(new Writer\Json(), 48);
        $writerFactory->addWriter(new Writer\Atom(), 32);
        $writerFactory->addWriter(new Writer\Form(), 24);
        $writerFactory->addWriter(new Writer\Jsonp(), 16);
        $writerFactory->addWriter(new Writer\Jsonx(), 15);
        $writerFactory->addWriter(new Writer\Soap(!empty($soapNamespace) ? $soapNamespace : 'http://phpsx.org/2014/data'), 8);
        $writerFactory->addWriter(new Writer\Xml(), 0);

        return $writerFactory;
    }
}
