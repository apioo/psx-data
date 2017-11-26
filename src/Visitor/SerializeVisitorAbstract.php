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

/**
 * SerializeVisitorAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SerializeVisitorAbstract extends VisitorAbstract
{
    /**
     * @var array
     */
    protected $objectStack = array();

    /**
     * @var int
     */
    protected $objectCount = -1;

    /**
     * @var array
     */
    protected $arrayStack = array();

    /**
     * @var int
     */
    protected $arrayCount = -1;

    /**
     * @var object
     */
    protected $lastObject;

    /**
     * @var array
     */
    protected $lastArray;

    /**
     * @var array
     */
    protected $stack = array();

    public function getObject()
    {
        return $this->lastObject;
    }

    public function getArray()
    {
        return $this->lastArray;
    }

    public function visitObjectStart($name)
    {
        $this->objectStack[] = $this->newObject();

        $this->objectCount++;
    }

    public function visitObjectEnd()
    {
        $this->lastObject = array_pop($this->objectStack);

        $this->objectCount--;
    }

    public function visitObjectValueStart($key, $value)
    {
        $this->stack[] = [$key, $value];
    }

    public function visitObjectValueEnd()
    {
        list($key, $value) = array_pop($this->stack);

        $this->addObjectValue($key, $this->getValue($value), $this->objectStack[$this->objectCount]);
    }

    public function visitArrayStart()
    {
        $this->arrayStack[] = $this->newArray();

        $this->arrayCount++;
    }

    public function visitArrayEnd()
    {
        $this->lastArray = array_pop($this->arrayStack);

        $this->arrayCount--;
    }

    public function visitArrayValueStart($value)
    {
        $this->stack[] = [$value];
    }

    public function visitArrayValueEnd()
    {
        list($value) = array_pop($this->stack);

        $this->addArrayValue($this->getValue($value), $this->arrayStack[$this->arrayCount]);
    }

    /**
     * Returns an new object instance
     *
     * @return mixed
     */
    abstract protected function newObject();

    /**
     * Adds an key value pair to the object
     *
     * @param string $key
     * @param mixed $value
     * @param mixed $object
     */
    abstract protected function addObjectValue($key, $value, &$object);

    /**
     * Returns an new array instance
     *
     * @return mixed
     */
    abstract protected function newArray();

    /**
     * Adds an value to an array
     *
     * @param mixed $value
     * @param mixed $array
     */
    abstract protected function addArrayValue($value, &$array);

    protected function newValue($value)
    {
        if ($value instanceof \DateTime) {
            return DateTime::getFormat($value);
        } elseif (is_scalar($value)) {
            return $value;
        } elseif (is_null($value)) {
            return $value;
        } else {
            return (string) $value;
        }
    }

    protected function getValue($value)
    {
        if (GraphTraverser::isObject($value)) {
            return $this->lastObject;
        } elseif (GraphTraverser::isArray($value)) {
            return $this->lastArray;
        } else {
            return $this->newValue($value);
        }
    }
}
