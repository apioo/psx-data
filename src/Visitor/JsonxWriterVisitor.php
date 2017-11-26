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

namespace PSX\Data\Visitor;

use PSX\Data\GraphTraverser;
use PSX\Data\VisitorAbstract;
use PSX\DateTime\DateTime;
use XMLWriter;

/**
 * JsonxWriterVisitor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonxWriterVisitor extends VisitorAbstract
{
    const XMLNS  = 'http://www.ibm.com/xmlns/prod/2009/jsonx';
    const PREFIX = 'json';

    /**
     * @var \XMLWriter
     */
    protected $writer;

    /**
     * @var integer
     */
    protected $level = 0;

    public function __construct(XMLWriter $writer)
    {
        $this->writer = $writer;
    }

    public function visitObjectStart($name)
    {
        if ($this->level == 0) {
            $this->writer->startElementNS(self::PREFIX, 'object', self::XMLNS);
        }

        $this->level++;
    }

    public function visitObjectEnd()
    {
        $this->level--;

        if ($this->level == 0) {
            $this->writer->endElement();
        }
    }

    public function visitObjectValueStart($key, $value)
    {
        $this->writer->startElementNS(self::PREFIX, $this->getDataType($value), null);
        $this->writer->writeAttribute('name', $key);
    }

    public function visitObjectValueEnd()
    {
        $this->writer->endElement();
    }

    public function visitArrayStart()
    {
        if ($this->level == 0) {
            $this->writer->startElementNS(self::PREFIX, 'array', self::XMLNS);
        }

        $this->level++;
    }

    public function visitArrayEnd()
    {
        $this->level--;

        if ($this->level == 0) {
            $this->writer->endElement();
        }
    }

    public function visitArrayValueStart($value)
    {
        $this->writer->startElementNS(self::PREFIX, $this->getDataType($value), null);
    }

    public function visitArrayValueEnd()
    {
        $this->writer->endElement();
    }

    public function visitValue($value)
    {
        if ($value instanceof \DateTime) {
            $value = DateTime::getFormat($value);
        }

        if (is_int($value) || is_float($value)) {
            $this->writer->text($value);
        } elseif (is_bool($value)) {
            $this->writer->text($value ? 'true' : 'false');
        } elseif (is_null($value)) {
        } else {
            $this->writer->text((string) $value);
        }
    }

    protected function getDataType($value)
    {
        if (GraphTraverser::isObject($value)) {
            return 'object';
        } elseif (GraphTraverser::isArray($value)) {
            return 'array';
        } elseif (is_int($value) || is_float($value)) {
            return 'number';
        } elseif (is_bool($value)) {
            return 'boolean';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            return 'string';
        }
    }
}
