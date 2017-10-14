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

namespace PSX\Data\Reader;

use PSX\Data\ReaderAbstract;
use PSX\Data\Util\CurveArray;
use PSX\Http\MediaType;

/**
 * Multipart
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Multipart extends ReaderAbstract
{
    /**
     * @var array
     */
    protected $files;

    /**
     * @var array
     */
    protected $post;

    /**
     * @param array|null $files
     */
    public function __construct(array $files = null, array $post = null)
    {
        $this->files = $files === null ? $_FILES : $files;
        $this->post  = $post  === null ? $_POST  : $post;
    }

    public function read($data)
    {
        // we dont support the array syntax
        $files = [];
        foreach ($this->files as $name => $file) {
            if (isset($file['error'])) {
                $files[$name] = $file;
            }
        }

        // in case of file upload we use the parsed data in the super globals
        $data = array_merge($files, $this->post);

        if (!empty($data)) {
            return CurveArray::objectify($data);
        } else {
            return null;
        }
    }

    public function isContentTypeSupported(MediaType $contentType)
    {
        return $contentType->getName() == 'multipart/form-data';
    }
}
