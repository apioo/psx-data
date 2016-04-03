<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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
use Traversable;
use PSX\Record\RecordInterface;

/**
 * GraphTraverser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class GraphTraverser
{
    public function traverse($record, VisitorInterface $visitor)
    {
        $record = self::reveal($record);

        if (!self::isObject($record)) {
            throw new InvalidArgumentException('Provided value must be an object type');
        }

        $this->traverseObject($record, $visitor);
    }

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

    protected function traverseValue($value, VisitorInterface $visitor)
    {
        if (self::isObject($value)) {
            $this->traverseObject($value, $visitor);
        } elseif (self::isArray($value)) {
            $visitor->visitArrayStart();

            foreach ($value as $val) {
                $val = self::reveal($val);

                $visitor->visitArrayValueStart($val);

                $this->traverseValue($val, $visitor);

                $visitor->visitArrayValueEnd();
            }

            $visitor->visitArrayEnd();
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

    public static function isObject($value)
    {
        return $value instanceof RecordInterface || $value instanceof \stdClass || (is_array($value) && CurveArray::isAssoc($value));
    }

    public static function isArray($value)
    {
        return is_array($value);
    }
}
