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

namespace PSX\Data\Writer;

use PSX\Data\GraphTraverser;
use PSX\Data\WriterInterface;
use XMLWriter;

/**
 * XmlWriterAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class XmlWriterAbstract implements WriterInterface
{
    protected $writer;

    /**
     * If an writer is given the result gets written to the XMLWriter and the
     * write method returns null. Otherwise the write method returns the xml as
     * string
     *
     * @param XMLWriter $writer
     */
    public function __construct(XMLWriter $writer = null)
    {
        $this->writer = $writer;
    }

    public function write($data)
    {
        $hasWriter = $this->writer !== null;
        $writer    = $this->writer ?: new XMLWriter();

        if (!$hasWriter) {
            $writer->openMemory();
            $writer->setIndent(true);
            $writer->startDocument('1.0', 'UTF-8');
        }

        if (GraphTraverser::isObject($data) || GraphTraverser::isArray($data)) {
            $graph = new GraphTraverser();
            $graph->traverse($data, $this->getVisitor($writer));
        } else {
            throw new \InvalidArgumentException('Value must be an array or object');
        }

        if (!$hasWriter) {
            $writer->endDocument();

            return $writer->outputMemory();
        } else {
            return null;
        }
    }

    /**
     * @param \XMLWriter $writer
     * @return \PSX\Data\VisitorInterface
     */
    abstract protected function getVisitor(XMLWriter $writer);
}
