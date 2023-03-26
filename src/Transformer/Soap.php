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

namespace PSX\Data\Transformer;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Data\Exception\InvalidDataException;
use RuntimeException;

/**
 * Transforms an incoming SOAP request
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Soap extends XmlArray
{
    const ENVELOPE_NS = 'http://schemas.xmlsoap.org/soap/envelope/';

    public function transform(mixed $data): \stdClass
    {
        if (!$data instanceof DOMDocument) {
            throw new InvalidDataException('Data must be an instanceof DOMDocument');
        }

        return $this->extractBody($data->documentElement);
    }

    /**
     * @throws InvalidDataException
     */
    protected function extractBody(DOMElement $element): \stdClass
    {
        $body = $element->getElementsByTagNameNS(self::ENVELOPE_NS, 'Body')->item(0);

        if ($body instanceof DOMElement) {
            $root = $this->findFirstElement($body);

            if ($root instanceof DOMElement) {
                return $this->recToXml($root);
            }

            return new \stdClass();
        } else {
            throw new InvalidDataException('Found no SOAP (' . self::ENVELOPE_NS . ') Body element');
        }
    }

    private function findFirstElement(DOMElement $element)
    {
        foreach ($element->childNodes as $childNode) {
            if ($childNode->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            if ($this->namespace !== null && $childNode->namespaceURI != $this->namespace) {
                continue;
            }

            return $childNode;
        }

        return null;
    }
}
