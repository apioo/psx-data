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
 * WriterFactory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WriterFactory
{
    protected $writers;
    protected $contentNegotiation = array();

    public function __construct()
    {
        $this->writers = new PriorityQueue();
    }

    public function addWriter(WriterInterface $writer, $priority = 0)
    {
        $this->writers->insert($writer, $priority);
    }

    public function getDefaultWriter(array $supportedWriter = null)
    {
        foreach ($this->writers as $writer) {
            $className = get_class($writer);

            if ($supportedWriter !== null && !in_array($className, $supportedWriter)) {
                continue;
            }

            return $writer;
        }

        return null;
    }

    public function getWriterByContentType($contentType, array $supportedWriter = null)
    {
        if (empty($contentType)) {
            return null;
        }

        $contentTypes = MediaType::parseList($contentType);

        // first we check all custom content negotiation rules
        if (!empty($this->contentNegotiation)) {
            foreach ($contentTypes as $contentType) {
                $writer = $this->getWriterFromContentNegotiation($contentType, $supportedWriter);

                if ($writer !== null) {
                    return $writer;
                }
            }
        }

        // then we ask every writer whether they support the content type
        foreach ($contentTypes as $contentType) {
            foreach ($this->writers as $writer) {
                if ($supportedWriter !== null && !in_array(get_class($writer), $supportedWriter)) {
                    continue;
                }

                if ($writer->isContentTypeSupported($contentType)) {
                    return $writer;
                }
            }
        }

        return null;
    }

    public function getWriterByInstance($className)
    {
        foreach ($this->writers as $writer) {
            if (get_class($writer) === $className) {
                return $writer;
            }
        }

        return null;
    }

    /**
     * Returns a writer class by the class short name
     * 
     * @param string $format
     * @return string|null
     */
    public function getWriterClassNameByFormat($format)
    {
        $format = strtolower($format);
        foreach ($this->writers as $writer) {
            $class = get_class($writer);
            $name  = strtolower(substr($class, strrpos($class, '\\') + 1));

            if ($name == $format) {
                return $class;
            }
        }

        return null;
    }

    /**
     * With this method you can set which writer should be used for an specific
     * content type. The content type can be i.e. text/plain or image/*
     *
     * @param string $contentType
     * @param string $writerClass
     */
    public function setContentNegotiation($contentType, $writerClass)
    {
        $this->contentNegotiation[$contentType] = $writerClass;
    }

    /**
     * Returns the fitting writer according to the content negotiation. If no
     * fitting writer could be found null gets returned
     *
     * @param \PSX\Http\MediaType $contentType
     * @param array $supportedWriter
     * @return \PSX\Data\WriterInterface
     */
    protected function getWriterFromContentNegotiation(MediaType $contentType, array $supportedWriter = null)
    {
        if (empty($this->contentNegotiation)) {
            return null;
        }

        foreach ($this->contentNegotiation as $acceptedContentType => $writerClass) {
            if ($supportedWriter !== null && !in_array($writerClass, $supportedWriter)) {
                continue;
            }

            $acceptedContentType = new MediaType($acceptedContentType);

            if ($acceptedContentType->match($contentType)) {
                $writer = $this->getWriterByInstance($writerClass);

                if ($writer !== null) {
                    return $writer;
                }
            }
        }

        return null;
    }
}
