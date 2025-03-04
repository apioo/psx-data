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

namespace PSX\Data\Reader;

use DOMDocument;
use PSX\Data\ReaderAbstract;
use PSX\Http\MediaType;

/**
 * Xml
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Xml extends ReaderAbstract
{
    public function read(string $data): mixed
    {
        if (!empty($data)) {
            $dom = new DOMDocument();
            $dom->encoding = 'UTF-8';
            $dom->loadXML($data);

            return $dom;
        } else {
            return null;
        }
    }

    public function isContentTypeSupported(MediaType $contentType): bool
    {
        return MediaType\Xml::isMediaType($contentType);
    }
}
