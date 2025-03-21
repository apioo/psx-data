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

namespace PSX\Data\Writer;

use PSX\Data\GraphTraverser;
use PSX\Data\Visitor;
use PSX\Http\MediaType;
use PSX\Model\Error;
use XMLWriter;

/**
 * Soap
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Soap extends Xml
{
    protected const MIME = 'application/soap+xml';

    private ?string $requestMethod;

    public function setRequestMethod(string $requestMethod)
    {
        $this->requestMethod = strtolower($requestMethod);
    }

    public function getRequestMethod(): ?string
    {
        return $this->requestMethod;
    }

    public function write(mixed $data): string
    {
        $xmlWriter = new XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->setIndent(true);
        $xmlWriter->startDocument('1.0', 'UTF-8');

        $xmlWriter->startElement('soap:Envelope');
        $xmlWriter->writeAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/soap/envelope/');

        if ($data instanceof Error) {
            $xmlWriter->startElement('soap:Body');
            $xmlWriter->startElement('soap:Fault');

            $xmlWriter->writeElement('faultcode', 'soap:Server');
            $xmlWriter->writeElement('faultstring', $data->getMessage());

            if ($data->getTrace()) {
                $xmlWriter->startElement('detail');

                $graph = new GraphTraverser();
                $graph->traverse($data, new Visitor\JsonxWriterVisitor($xmlWriter));

                $xmlWriter->endElement();
            }

            $xmlWriter->endElement();
            $xmlWriter->endElement();
        } else {
            $xmlWriter->startElement('soap:Body');

            $graph = new GraphTraverser();
            $graph->traverse($data, new Visitor\JsonxWriterVisitor($xmlWriter));

            $xmlWriter->endElement();
        }

        $xmlWriter->endElement();
        $xmlWriter->endDocument();

        return $xmlWriter->outputMemory();
    }

    public function isContentTypeSupported(MediaType $contentType): bool
    {
        return $contentType->getName() == self::MIME;
    }

    public function getContentType(): string
    {
        return 'text/xml';
    }
}
