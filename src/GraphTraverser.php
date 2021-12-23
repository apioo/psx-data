<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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
    public function traverse(mixed $record, VisitorInterface $visitor): void
    {
        $this->traverseValue(self::reveal($record), $visitor);
    }

    private function traverseObject(mixed $object, VisitorInterface $visitor): void
    {
        $name = null;
        if ($object instanceof RecordInterface) {
            $properties = $object->getProperties();
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

    private function traverseArray(mixed $values, VisitorInterface $visitor): void
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

    private function traverseValue(mixed $value, VisitorInterface $visitor): void
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
     */
    public static function reveal(mixed $object): mixed
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
     */
    public static function isObject(mixed $value): bool
    {
        return $value instanceof RecordInterface || $value instanceof \stdClass || (is_array($value) && CurveArray::isAssoc($value));
    }

    /**
     * Checks whether a value is an array type
     */
    public static function isArray(mixed $value): bool
    {
        return is_array($value);
    }

    /**
     * Checks whether a value is empty
     */
    public static function isEmpty(mixed $value): bool
    {
        if (empty($value)) {
            return true;
        } elseif ($value instanceof \stdClass) {
            return count((array) $value) === 0;
        } elseif ($value instanceof RecordInterface) {
            return $value->isEmpty();
        }

        return false;
    }
}
