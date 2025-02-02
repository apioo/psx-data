<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Data\Exception\InvalidDataException;
use PSX\Data\Exception\ParseException;
use PSX\Data\Exception\ReaderNotFoundException;
use PSX\Data\Exception\ReadException;
use PSX\Data\Exception\WriteException;
use PSX\Data\Exception\WriterNotFoundException;
use PSX\Http\MediaType;
use PSX\Record\RecordInterface;
use PSX\Schema\Exception\InvalidSchemaException;
use PSX\Schema\Exception\TraverserException;
use PSX\Schema\SchemaInterface;
use PSX\Schema\SchemaSource;
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
 * @link    https://phpsx.org
 */
class Processor
{
    private Configuration $config;
    private ExporterInterface $exporter;
    private SchemaTraverser $traverser;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
        $this->exporter = new Exporter\Popo();
        $this->traverser = new SchemaTraverser();
    }

    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    /**
     * Tries to read the provided payload with a fitting reader. The reader
     * depends on the content type of the payload or on the reader type if
     * explicit specified. Then we validate the data according to the provided
     * schema
     *
     * @throws ReadException
     * @throws ReaderNotFoundException
     */
    public function read(mixed $schema, Payload $payload, ?SchemaVisitorInterface $visitor = null): mixed
    {
        try {
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
        } catch (TraverserException|ParseException|InvalidSchemaException|InvalidDataException $e) {
            throw new ReadException($e->getMessage(), previous: $e);
        }
    }

    /**
     * Parses the payload and returns the data in a normalized format
     *
     * @throws ParseException
     * @throws ReaderNotFoundException
     */
    public function parse(Payload $payload): mixed
    {
        try {
            $reader = $this->getReader($payload->getContentType(), $payload->getRwType(), $payload->getRwSupported());
            $data = $reader->read($payload->getData());

            $transformer = $payload->getTransformer();

            if ($transformer === null) {
                $transformer = $this->getDefaultTransformer($payload->getContentType());
            }

            if ($transformer instanceof TransformerInterface) {
                $data = $transformer->transform($data);
            }

            return $data;
        } catch (InvalidDataException $e) {
            throw new ParseException($e->getMessage(), previous: $e);
        }
    }

    /**
     * Writes the payload with a fitting writer and returns the result as
     * string. The writer depends on the content type of the payload or on the
     * writer type if explicit specified
     *
     * @throws WriteException
     * @throws WriterNotFoundException
     */
    public function write(Payload $payload): string
    {
        try {
            $data = $this->transform($payload->getData());
            $writer = $this->getWriter($payload->getContentType(), $payload->getRwType(), $payload->getRwSupported());

            return $writer->write($data);
        } catch (InvalidDataException $e) {
            throw new WriteException($e->getMessage(), previous: $e);
        }
    }

    /**
     * Returns the data of the payload in a normalized format
     *
     * @throws InvalidDataException
     */
    public function transform(mixed $data): array|\stdClass|RecordInterface
    {
        return $this->exporter->export($data);
    }

    /**
     * Returns a fitting reader for the given content type or throws an
     * unsupported media exception. It is also possible to explicit select a
     * reader by providing the class name as reader type.
     *
     * @throws ReaderNotFoundException
     */
    public function getReader(?string $contentType, ?string $readerType = null, ?array $supportedReader = null): ReaderInterface
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
            throw new ReaderNotFoundException('Could not find fitting data reader for content type');
        }

        return $reader;
    }

    /**
     * Returns a fitting writer for the given content type or throws an not
     * acceptable exception. It is also possible to explicit select a writer by
     * providing the class name as writer type.
     *
     * @throws WriterNotFoundException
     */
    public function getWriter(?string $contentType, ?string $writerType = null, ?array $supportedWriter = null): WriterInterface
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
            throw new WriterNotFoundException('Could not find fitting data writer');
        }

        return $writer;
    }

    protected function getDefaultTransformer(?string $contentType): ?TransformerInterface
    {
        if (empty($contentType)) {
            return null;
        }

        $mime = MediaType::parse($contentType);

        if ($mime->getName() == 'application/jsonx+xml') {
            return new Transformer\Jsonx();
        } elseif ($mime->getName() == 'application/soap+xml') {
            return new Transformer\Soap();
        } elseif (in_array($mime->getName(), MediaType\Xml::getMediaTypes()) ||
            str_ends_with($mime->getSubType(), '+xml') ||
            str_ends_with($mime->getSubType(), '/xml')) {
            return new Transformer\XmlArray();
        }

        return null;
    }

    /**
     * @throws InvalidDataException
     * @throws InvalidSchemaException
     */
    protected function getSchema(mixed $schema): SchemaInterface
    {
        if (is_string($schema) || $schema instanceof SchemaSource) {
            return $this->config->getSchemaManager()->getSchema($schema);
        } elseif ($schema instanceof SchemaInterface) {
            return $schema;
        } else {
            throw new InvalidDataException('Schema must be either a string or ' . SchemaInterface::class);
        }
    }
}
