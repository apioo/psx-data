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
use PSX\Http\MediaType;

/**
 * WriterInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
interface WriterInterface
{
    const FORM  = Writer\Form::class;
    const JSON  = Writer\Json::class;
    const JSONP = Writer\Jsonp::class;
    const JSONX = Writer\Jsonx::class;
    const SOAP  = Writer\Soap::class;
    const XML   = Writer\Xml::class;

    /**
     * Returns the string representation of this record from the writer
     *
     * @throws InvalidDataException
     */
    public function write(mixed $data): string;

    /**
     * Returns whether the content type is supported by this writer
     */
    public function isContentTypeSupported(MediaType $contentType): bool;

    /**
     * Returns the content type of this writer
     */
    public function getContentType(): string;
}
