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

use PSX\Data\VisitorAbstract;
use PSX\DateTime\DateTime;
use XMLWriter;

/**
 * XmlWriterVisitor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlWriterVisitor extends VisitorAbstract
{
    /**
     * @var \XMLWriter
     */
    protected $writer;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $objectKey;

    /**
     * @var array
     */
    protected $arrayKey = array();

    /**
     * @var integer
     */
    protected $level = 0;

    /**
     * @var boolean
     */
    protected $arrayStart = false;

    /**
     * @var boolean
     */
    protected $arrayEnd = false;

    public function __construct(XMLWriter $writer, $namespace = null)
    {
        $this->writer    = $writer;
        $this->namespace = $namespace;
    }

    public function visitObjectStart($name)
    {
        if ($this->level == 0) {
            $this->writer->startElement($name);

            if ($this->namespace !== null) {
                $this->writer->writeAttribute('xmlns', $this->namespace);
            }
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
        // replace all non alnum characters
        $key = preg_replace('/[^A-Za-z0-9]/', '_', $key);

        // element names can not start with an digit
        if (isset($key[0]) && ctype_digit($key[0])) {
            $key = '_' . $key;
        }

        $this->writer->startElement($this->objectKey = $key);
    }

    public function visitObjectValueEnd()
    {
        if (!$this->arrayEnd) {
            $this->writer->endElement();
        }

        $this->arrayEnd = false;
    }

    public function visitArrayStart()
    {
        if ($this->level == 0) {
            $this->writer->startElement('collection');
        } else {
            $this->arrayStart = true;

            array_push($this->arrayKey, $this->objectKey);
        }
    }

    public function visitArrayEnd()
    {
        if ($this->level == 0) {
            $this->writer->endElement();
        } else {
            $this->arrayEnd = true;

            array_pop($this->arrayKey);
        }
    }

    public function visitArrayValueStart($value)
    {
        if ($this->level == 0) {
            // noop
        } elseif (!$this->arrayStart) {
            $this->writer->startElement(end($this->arrayKey));
        }

        $this->arrayStart = false;
    }

    public function visitArrayValueEnd()
    {
        if ($this->level == 0) {
            // noop
        } else {
            $this->writer->endElement();
        }
    }

    public function visitValue($value)
    {
        $this->writer->text($this->getValue($value));
    }

    protected function getValue($value)
    {
        if ($value instanceof \DateTime) {
            return DateTime::getFormat($value);
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } else {
            return (string) $value;
        }
    }
}
