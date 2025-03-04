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

use PSX\Data\Body;
use PSX\Data\Multipart\File;
use PSX\Data\ReaderAbstract;
use PSX\Data\Util\CurveArray;
use PSX\Http\MediaType;

/**
 * Multipart
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Multipart extends ReaderAbstract
{
    private array $files;
    private array $post;

    public function __construct(?array $files = null, ?array $post = null)
    {
        $this->files = $files === null ? $_FILES : $files;
        $this->post  = $post  === null ? $_POST  : $post;
    }

    public function read(string $data): mixed
    {
        $multipart = new Body\Multipart();

        foreach ($this->files as $name => $file) {
            if (isset($file['error'])) {
                $multipart->addPart($name, File::fromArray($file));
            }
        }

        if ($multipart->hasFile()) {
            foreach ($this->post as $name => $value) {
                $multipart->addPart($name, $value);
            }

            return $multipart;
        } else {
            // we use the multipart body only in case there are file uploads, otherwise we return a stdClass similar to
            // the form reader, this is needed since some HTTP clients like RestSharp always uses a multipart content
            // type containing the form values
            return !empty($this->post) ? CurveArray::objectify($this->post) : null;
        }
    }

    public function isContentTypeSupported(MediaType $contentType): bool
    {
        return $contentType->getName() == 'multipart/form-data';
    }
}
