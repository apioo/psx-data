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

namespace PSX\Data\Visitor;

use PSX\Data\GraphTraverser;
use PSX\Data\VisitorAbstract;
use PSX\DateTime\DateTime;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\Period;

/**
 * SerializeVisitorAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class SerializeVisitorAbstract extends VisitorAbstract
{
    protected array $objectStack = [];
    private int $objectCount = -1;
    private array $arrayStack = [];
    private int $arrayCount = -1;
    private object $lastObject;
    private array $lastArray;
    private array $stack = [];

    public function __construct()
    {
        $this->lastObject = new \stdClass();
        $this->lastArray = [];
    }

    public function getObject(): object
    {
        return $this->lastObject;
    }

    public function getArray(): array
    {
        return $this->lastArray;
    }

    public function visitObjectStart()
    {
        $this->objectStack[] = $this->newObject();

        $this->objectCount++;
    }

    public function visitObjectEnd()
    {
        $this->lastObject = array_pop($this->objectStack);

        $this->objectCount--;
    }

    public function visitObjectValueStart(string $key, mixed $value)
    {
        $this->stack[] = [$key, $value];
    }

    public function visitObjectValueEnd()
    {
        $result = array_pop($this->stack);
        if (is_array($result)) {
            [$key, $value] = $result;
            $this->addObjectValue($key, $this->getValue($value), $this->objectStack[$this->objectCount]);
        }
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
        $result = array_pop($this->stack);
        if (is_array($result)) {
            [$value] = $result;
            $this->addArrayValue($this->getValue($value), $this->arrayStack[$this->arrayCount]);
        }
    }

    /**
     * Returns a new object instance
     */
    abstract protected function newObject(): object;

    /**
     * Adds a key value pair to the object
     */
    abstract protected function addObjectValue(string $key, mixed $value, mixed &$object);

    /**
     * Returns n new array instance
     */
    abstract protected function newArray(): array;

    /**
     * Adds a value to an array
     *
     * @param mixed $value
     * @param mixed $array
     */
    abstract protected function addArrayValue(string $value, mixed &$array);

    protected function newValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return LocalDateTime::from($value)->toString();
        } elseif ($value instanceof \DateInterval) {
            return Period::from($value)->toString();
        } elseif (is_scalar($value)) {
            return $value;
        } elseif (is_null($value)) {
            return null;
        } else {
            return (string) $value;
        }
    }

    protected function getValue(mixed $value): mixed
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
