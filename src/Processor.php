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

use PSX\Http\Exception as StatusCode;
use PSX\Http\MediaType;
use PSX\Schema\Parser;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaTraverser;
use PSX\Schema\Visitor\TypeVisitor;
use PSX\Schema\VisitorInterface as SchemaVisitorInterface;

/**
 * Main entry point of the data library. Through the processor it is possible to
 * reade and write arbitrary data in conformance to a specific schema.
 *
 * <code>
 * $config    = Configuration::createDefault();
 * $processor = new Processor($config);
 *
 * // reads the json data into a custom model class
 * $model = $processor->read(Some\Model::class, Payload::json('{"foo": "bar"}'));
 *
 * // writes the model back into json
 * $response = $processor->write($model);
 * </code>
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Processor
{
    /**
     * @var \PSX\Data\Configuration
     */
    protected $config;

    /**
     * @var \PSX\Schema\ParserInterface
     */
    protected $parser;

    /**
     * @var \PSX\Data\ExporterInterface
     */
    protected $exporter;

    /**
     * @var \PSX\Schema\SchemaTraverser
     */
    protected $traverser;

    public function __construct(Configuration $config)
    {
        $this->config    = $config;
        $this->parser    = new Parser\Popo($this->config->getAnnotationReader());
        $this->exporter  = new Exporter\Popo($this->config->getAnnotationReader());
        $this->traverser = new SchemaTraverser();
    }

    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Tries to read the provided payload with a fitting reader. The reader
     * depends on the content type of the payload or on the reader type if
     * explicit specified. Then we validate the data according to the provided
     * schema
     *
     * @param string $schema
     * @param \PSX\Data\Payload $payload
     * @param \PSX\Schema\VisitorInterface $visitor
     * @return mixed
     * @throws \PSX\Data\InvalidDataException
     * @throws \PSX\Http\Exception\UnsupportedMediaTypeException
     */
    public function read($schema, Payload $payload, SchemaVisitorInterface $visitor = null)
    {
        $data   = $this->parse($payload);
        $schema = $this->getSchema($schema);

        if ($visitor === null) {
            $visitor = new TypeVisitor();
        }

        return $this->traverser->traverse(
            $data,
            $schema,
            $visitor
        );
    }

    /**
     * Parses the payload and returns the data in a normalized format
     *
     * @param \PSX\Data\Payload $payload
     * @return \stdClass
     * @throws StatusCode\UnsupportedMediaTypeException
     */
    public function parse(Payload $payload)
    {
        $reader = $this->getReader($payload->getContentType(), $payload->getRwType(), $payload->getRwSupported());
        $data   = $reader->read($payload->getData());

        $transformer = $payload->getTransformer();

        if ($transformer === null) {
            $transformer = $this->getDefaultTransformer($payload->getContentType());
        }

        if ($transformer instanceof TransformerInterface) {
            $data = $transformer->transform($data);
        }

        return $data;
    }

    /**
     * Writes the payload with a fitting writer and returns the result as
     * string. The writer depends on the content type of the payload or on the
     * writer type if explicit specified
     *
     * @param \PSX\Data\Payload $payload
     * @return string
     * @throws \PSX\Data\InvalidDataException
     * @throws \PSX\Http\Exception\NotAcceptableException
     */
    public function write(Payload $payload)
    {
        $data   = $payload->getData();
        $data   = $this->transform($data);
        $writer = $this->getWriter($payload->getContentType(), $payload->getRwType(), $payload->getRwSupported());

        return $writer->write($data);
    }

    /**
     * Returns the data of the payload in a normalized format
     *
     * @param mixed $payload
     * @return \stdClass
     */
    public function transform($data)
    {
        return $this->exporter->export($data);
    }

    /**
     * Returns a fitting reader for the given content type or throws an
     * unsupported media exception. It is also possible to explicit select a
     * reader by providing the class name as reader type.
     *
     * @param string $contentType
     * @param string $readerType
     * @param array $supportedReader
     * @return \PSX\Data\ReaderInterface
     */
    public function getReader($contentType, $readerType = null, array $supportedReader = null)
    {
        if ($readerType === null) {
            $reader = $this->config->getReaderFactory()->getReaderByContentType($contentType, $supportedReader);
        } else {
            $reader = $this->config->getReaderFactory()->getReaderByInstance($readerType);
        }

        if ($reader === null) {
            $reader = $this->config->getReaderFactory()->getDefaultReader($supportedReader);
        }

        if (!$reader instanceof ReaderInterface) {
            throw new StatusCode\UnsupportedMediaTypeException('Could not find fitting data reader');
        }

        return $reader;
    }

    /**
     * Returns a fitting writer for the given content type or throws an not
     * acceptable exception. It is also possible to explicit select a writer by
     * providing the class name as writer type.
     *
     * @param string $contentType
     * @param string $writerType
     * @param array $supportedWriter
     * @return \PSX\Data\WriterInterface
     */
    public function getWriter($contentType, $writerType = null, array $supportedWriter = null)
    {
        if ($writerType === null) {
            $writer = $this->config->getWriterFactory()->getWriterByContentType($contentType, $supportedWriter);
        } else {
            $writer = $this->config->getWriterFactory()->getWriterByInstance($writerType);
        }

        if ($writer === null) {
            $writer = $this->config->getWriterFactory()->getDefaultWriter($supportedWriter);
        }

        if (!$writer instanceof WriterInterface) {
            throw new StatusCode\NotAcceptableException('Could not find fitting data writer');
        }

        return $writer;
    }

    protected function getDefaultTransformer($contentType)
    {
        $mime = new MediaType($contentType);

        if ($mime->getName() == 'application/atom+xml') {
            return new Transformer\Atom();
        } elseif ($mime->getName() == 'application/jsonx+xml') {
            return new Transformer\Jsonx();
        } elseif ($mime->getName() == 'application/rss+xml') {
            return new Transformer\Rss();
        } elseif ($mime->getName() == 'application/soap+xml') {
            return new Transformer\Soap($this->config->getNamespace());
        } elseif (in_array($mime->getName(), MediaType\Xml::getMediaTypes()) ||
            substr($mime->getSubType(), -4) == '+xml' ||
            substr($mime->getSubType(), -4) == '/xml') {
            return new Transformer\XmlArray();
        }

        return null;
    }

    protected function getSchema($schema)
    {
        if (is_string($schema)) {
            return $this->config->getSchemaManager()->getSchema($schema);
        } elseif ($schema instanceof SchemaInterface) {
            return $schema;
        } else {
            throw new InvalidDataException('Schema must be either a string or \PSX\Schema\SchemaInterface');
        }
    }
}
