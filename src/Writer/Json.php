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

use PSX\Data\Exception\InvalidDataException;
use PSX\Data\GraphTraverser;
use PSX\Data\Visitor;
use PSX\Data\WriterInterface;
use PSX\Http\MediaType;
use PSX\Json\Parser;

/**
 * Json
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Json implements WriterInterface
{
    protected const MIME = 'application/json';

    public function write(mixed $data): string
    {
        $visitor = new Visitor\StdClassSerializeVisitor();

        (new GraphTraverser())->traverse($data, $visitor);

        if (GraphTraverser::isObject($data)) {
            $value = $visitor->getObject();
        } elseif (GraphTraverser::isArray($data)) {
            $value = $visitor->getArray();
        } else {
            throw new InvalidDataException('Value must be an array or object');
        }

        return Parser::encode($value, JSON_PRETTY_PRINT);
    }

    public function isContentTypeSupported(MediaType $contentType): bool
    {
        return MediaType\Json::isMediaType($contentType);
    }

    public function getContentType(): string
    {
        return self::MIME;
    }
}
