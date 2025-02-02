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

use PSX\Data\Util\PriorityQueue;
use PSX\Http\MediaType;

/**
 * ReaderFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ReaderFactory
{
    private PriorityQueue $readers;

    public function __construct()
    {
        $this->readers = new PriorityQueue();
    }

    public function addReader(ReaderInterface $reader, int $priority = 0): void
    {
        $this->readers->insert($reader, $priority);
    }

    public function getDefaultReader(?array $supportedReader = null): ?ReaderInterface
    {
        foreach ($this->readers as $reader) {
            $className = get_class($reader);

            if ($supportedReader !== null && !in_array($className, $supportedReader)) {
                continue;
            }

            return $reader;
        }

        return null;
    }

    public function getReaderByContentType(?string $contentType, ?array $supportedReader = null): ?ReaderInterface
    {
        if (empty($contentType)) {
            return null;
        }

        $contentType = MediaType::parse($contentType);

        foreach ($this->readers as $reader) {
            if ($supportedReader !== null && !in_array(get_class($reader), $supportedReader)) {
                continue;
            }

            if ($reader->isContentTypeSupported($contentType)) {
                return $reader;
            }
        }

        return null;
    }

    public function getReaderByInstance(string $className): ?ReaderInterface
    {
        foreach ($this->readers as $reader) {
            if (get_class($reader) === $className) {
                return $reader;
            }
        }

        return null;
    }
}
