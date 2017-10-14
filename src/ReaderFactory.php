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

use PSX\Data\Util\PriorityQueue;
use PSX\Http\MediaType;

/**
 * ReaderFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ReaderFactory
{
    /**
     * @var \PSX\Data\ReaderInterface[]
     */
    protected $readers;

    public function __construct()
    {
        $this->readers = new PriorityQueue();
    }

    /**
     * @param \PSX\Data\ReaderInterface $reader
     * @param integer $priority
     */
    public function addReader(ReaderInterface $reader, $priority = 0)
    {
        $this->readers->insert($reader, $priority);
    }

    /**
     * @param array $supportedReader
     * @return \PSX\Data\ReaderInterface
     */
    public function getDefaultReader(array $supportedReader = null)
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

    /**
     * @param string $contentType
     * @param array $supportedReader
     * @return \PSX\Data\ReaderInterface|null
     */
    public function getReaderByContentType($contentType, array $supportedReader = null)
    {
        if (empty($contentType)) {
            return null;
        }

        $contentType = new MediaType($contentType);

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

    /**
     * @param string $className
     * @return \PSX\Data\ReaderInterface|null
     */
    public function getReaderByInstance($className)
    {
        foreach ($this->readers as $reader) {
            if (get_class($reader) === $className) {
                return $reader;
            }
        }

        return null;
    }
}
