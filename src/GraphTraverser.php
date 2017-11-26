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

use ArrayObject;
use InvalidArgumentException;
use JsonSerializable;
use PSX\Data\Util\CurveArray;
use PSX\Record\RecordInterface;
use Traversable;

/**
 * GraphTraverser
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GraphTraverser
{
    /**
     * @param mixed $record
     * @param \PSX\Data\VisitorInterface $visitor
     */
    public function traverse($record, VisitorInterface $visitor)
    {
        $this->traverseValue(self::reveal($record), $visitor);
    }

    /**
     * @param object $object
     * @param \PSX\Data\VisitorInterface $visitor
     */
    protected function traverseObject($object, VisitorInterface $visitor)
    {
        $name = null;
        if ($object instanceof RecordInterface) {
            $properties = $object->getProperties();
            $name       = $object->getDisplayName();
        } else {
            $properties = (array) $object;
        }

        if (empty($name)) {
            $name = 'record';
        }

        $visitor->visitObjectStart($name);

        foreach ($properties as $key => $value) {
            $value = self::reveal($value);

            $visitor->visitObjectValueStart($key, $value);

            $this->traverseValue($value, $visitor);

            $visitor->visitObjectValueEnd();
        }

        $visitor->visitObjectEnd();
    }

    /**
     * @param array $values
     * @param \PSX\Data\VisitorInterface $visitor
     */
    protected function traverseArray($values, VisitorInterface $visitor)
    {
        $visitor->visitArrayStart();

        foreach ($values as $value) {
            $value = self::reveal($value);

            $visitor->visitArrayValueStart($value);

            $this->traverseValue($value, $visitor);

            $visitor->visitArrayValueEnd();
        }

        $visitor->visitArrayEnd();
    }

    /**
     * @param mixed $value
     * @param \PSX\Data\VisitorInterface $visitor
     */
    protected function traverseValue($value, VisitorInterface $visitor)
    {
        if (self::isObject($value)) {
            $this->traverseObject($value, $visitor);
        } elseif (self::isArray($value)) {
            $this->traverseArray($value, $visitor);
        } else {
            $visitor->visitValue($value);
        }
    }

    /**
     * Method which reveals the true value of an object if it has a known
     * interface. Note this resolves also all Traversable instances to an array
     *
     * @param mixed $object
     * @return mixed
     */
    public static function reveal($object)
    {
        if ($object instanceof RecordInterface) {
            return $object;
        } elseif ($object instanceof JsonSerializable) {
            return $object->jsonSerialize();
        } elseif ($object instanceof ArrayObject) {
            return $object->getArrayCopy();
        } elseif ($object instanceof Traversable) {
            return iterator_to_array($object);
        }

        return $object;
    }

    /**
     * Checks whether a value is an object type
     * 
     * @param mixed $value
     * @return boolean
     */
    public static function isObject($value)
    {
        return $value instanceof RecordInterface || $value instanceof \stdClass || (is_array($value) && CurveArray::isAssoc($value));
    }

    /**
     * Checks whether a value is an array type
     * 
     * @param mixed $value
     * @return boolean
     */
    public static function isArray($value)
    {
        return is_array($value);
    }

    /**
     * Checks whether a value is empty
     * 
     * @param mixed $value
     * @return boolean
     */
    public static function isEmpty($value)
    {
        if (empty($value)) {
            return true;
        } elseif ($value instanceof \stdClass) {
            return count((array) $value) === 0;
        } elseif ($value instanceof RecordInterface) {
            return count($value->getProperties()) === 0;
        }

        return false;
    }
}
