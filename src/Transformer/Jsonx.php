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
use PSX\Data\TransformerInterface;

/**
 * Jsonx
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 * @see     https://tools.ietf.org/html/draft-rsalz-jsonx-00
 */
class Jsonx implements TransformerInterface
{
    public function transform(mixed $data): \stdClass
    {
        if (!$data instanceof DOMDocument) {
            throw new InvalidDataException('Data must be an instanceof DOMDocument');
        }

        return $this->recToXml($data->documentElement);
    }

    /**
     * @throws InvalidDataException
     */
    private function recToXml(DOMElement $element): \stdClass
    {
        if ($element->localName != 'object') {
            throw new InvalidDataException('Root element must be an object');
        }

        $value = $this->getValue($element);
        if (!$value instanceof \stdClass) {
            throw new InvalidDataException('Root element must be an object');
        }

        return $value;
    }

    /**
     * @throws InvalidDataException
     */
    private function getValue(DOMElement $node)
    {
        switch ($node->localName) {
            case 'object':
                return $this->getObject($node);

            case 'array':
                return $this->getArray($node);

            case 'boolean':
                return $node->textContent == 'false' ? false : (boolean) $node->textContent;

            case 'string':
                return $node->textContent;

            case 'number':
                return str_contains($node->textContent, '.') ? (float) $node->textContent : (int) $node->textContent;

            case 'null':
                return null;

            default:
                throw new InvalidDataException('Invalid element name');
        }
    }

    /**
     * @throws InvalidDataException
     */
    private function getObject(DOMElement $element): \stdClass
    {
        $result = new \stdClass();

        foreach ($element->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }

            $name = $node->getAttribute('name');

            if (!empty($name)) {
                $result->$name = $this->getValue($node);
            }
        }

        return $result;
    }

    /**
     * @throws InvalidDataException
     */
    private function getArray(DOMElement $element): array
    {
        $result = array();

        foreach ($element->childNodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }

            $result[] = $this->getValue($node);
        }

        return $result;
    }
}
