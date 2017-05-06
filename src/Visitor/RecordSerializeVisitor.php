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

use PSX\Record\Record;
use PSX\Record\RecordInterface;

/**
 * Creates a new object tree using a record as object representation
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordSerializeVisitor extends SerializeVisitorAbstract
{
    protected $root;

    public function __construct(RecordInterface $root = null)
    {
        $this->root = $root;
    }

    protected function newObject()
    {
        if ($this->root !== null && count($this->objectStack) == 0) {
            return $this->root;
        } else {
            return new Record();
        }
    }

    protected function addObjectValue($key, $value, &$object)
    {
        $object->setProperty($key, $value);
    }

    protected function newArray()
    {
        return array();
    }

    protected function addArrayValue($value, &$array)
    {
        $array[] = $value;
    }
}
