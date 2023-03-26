<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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
    private SchemaManagerInterface $schemaManager;
    private ReaderFactory $readerFactory;
    private WriterFactory $writerFactory;

    public function __construct(SchemaManagerInterface $schemaManager, ReaderFactory $readerFactory, WriterFactory $writerFactory)
    {
        $this->schemaManager = $schemaManager;
        $this->readerFactory = $readerFactory;
        $this->writerFactory = $writerFactory;
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->schemaManager;
    }

    public function setReaderFactory(ReaderFactory $readerFactory): void
    {
        $this->readerFactory = $readerFactory;
    }

    public function getReaderFactory(): ReaderFactory
    {
        return $this->readerFactory;
    }

    public function setWriterFactory(WriterFactory $writerFactory): void
    {
        $this->writerFactory = $writerFactory;
    }

    public function getWriterFactory(): WriterFactory
    {
        return $this->writerFactory;
    }

    public static function createDefault(SchemaManagerInterface $schemaManager): self
    {
        return new self(
            $schemaManager,
            self::createDefaultReaderFactory(),
            self::createDefaultWriterFactory()
        );
    }

    protected static function createDefaultReaderFactory(): ReaderFactory
    {
        $readerFactory = new ReaderFactory();
        $readerFactory->addReader(new Reader\Json(), 16);
        $readerFactory->addReader(new Reader\Form(), 8);
        $readerFactory->addReader(new Reader\Multipart(), 1);
        $readerFactory->addReader(new Reader\Xml(), 0);

        return $readerFactory;
    }

    protected static function createDefaultWriterFactory(): WriterFactory
    {
        $writerFactory = new WriterFactory();
        $writerFactory->addWriter(new Writer\Json(), 48);
        $writerFactory->addWriter(new Writer\Form(), 24);
        $writerFactory->addWriter(new Writer\Jsonp(), 16);
        $writerFactory->addWriter(new Writer\Jsonx(), 15);
        $writerFactory->addWriter(new Writer\Soap(), 8);
        $writerFactory->addWriter(new Writer\Xml(), 0);

        return $writerFactory;
    }
}
