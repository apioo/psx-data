<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2017 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Http\MediaType;

/**
 * ReaderInterface
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface ReaderInterface
{
    const FORM = 'PSX\Data\Reader\Form';
    const JSON = 'PSX\Data\Reader\Json';
    const MULTIPART = 'PSX\Data\Reader\Multipart';
    const XML = 'PSX\Data\Reader\Xml';

    /**
     * Transforms the $request into an parseable form this can be an array
     * or DOMDocument etc.
     *
     * @param string $data
     * @return mixed
     */
    public function read($data);

    /**
     * Returns whether the content type is supported by this reader
     *
     * @param \PSX\Http\MediaType $contentType
     * @return boolean
     */
    public function isContentTypeSupported(MediaType $contentType);
}
