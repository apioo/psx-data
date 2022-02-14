<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Data\Multipart;

use PSX\Data\Exception\UploadException;

/**
 * Body
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Body implements \JsonSerializable
{
    private array $parts = [];

    public function addPart(string $name, mixed $value): void
    {
        $this->parts[$name] = $value;
    }

    public function getPart(string $name): mixed
    {
        return $this->parts[$name] ?? null;
    }

    public function isFile(string $name): bool
    {
        return isset($this->parts[$name]) && $this->parts[$name] instanceof File;
    }

    public function getFile(string $name): File
    {
        if (isset($this->parts[$name]) && $this->parts[$name] instanceof File) {
            return $this->parts[$name];
        } else {
            throw new UploadException('No file was uploaded for the field ' . $name);
        }
    }

    public function __get($name)
    {
        return $this->parts[$name] ?? null;
    }

    public function jsonSerialize(): array
    {
        return $this->parts;
    }
}