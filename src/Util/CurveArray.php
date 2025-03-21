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

namespace PSX\Data\Util;

use InvalidArgumentException;

/**
 * CurveArray
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class CurveArray
{
    /**
     * Converts a flat array into a nested object using a separator
     */
    public static function nest(array $data, string $separator = '_'): array|\stdClass
    {
        if (self::isAssoc($data)) {
            $result = new \stdClass();

            foreach ($data as $key => $value) {
                if (($pos = strpos($key, $separator)) !== false) {
                    $subKey = substr($key, 0, $pos);

                    if (!isset($result->{$subKey})) {
                        $result->{$subKey} = self::nest(self::getParts($data, $subKey . $separator), $separator);
                    }
                } else {
                    $result->{$key} = $value;
                }
            }

            return $result;
        } else {
            $result = [];

            foreach ($data as $value) {
                $result[] = self::nest($value, $separator);
            }

            return $result;
        }
    }

    /**
     * Converts a nested array into a flat using a separator. The prefix and
     * result parameter are used internally for performance reason and should
     * not be used
     */
    public static function flatten(array|\stdClass $data, string $separator = '_', ?string $prefix = null, array &$result = []): array
    {
        if ($data instanceof \stdClass) {
            $data = (array) $data;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof \stdClass || is_array($value)) {
                self::flatten($value, $separator, $prefix . $key . $separator, $result);
            } else {
                $result[$prefix . $key] = $value;
            }
        }

        return $result;
    }

    /**
     * Replaces all associative arrays with stdClass in an arbitrary array structure
     */
    public static function objectify(array $data): \stdClass|array
    {
        if (self::isAssoc($data)) {
            $result = new \stdClass();
            foreach ($data as $key => $value) {
                $result->{$key} = is_array($value) ? self::objectify($value) : $value;
            }

            return $result;
        } else {
            $result = [];
            foreach ($data as $value) {
                $result[] = is_array($value) ? self::objectify($value) : $value;
            }

            return $result;
        }
    }

    /**
     * Returns whether an array is index based or associative
     */
    public static function isAssoc(array $array): bool
    {
        if (empty($array)) {
            return false;
        }

        if (function_exists('array_is_list')) {
            return !array_is_list($array);
        }

        if (isset($array[0])) {
            $n = count($array) - 1;

            return array_sum(array_keys($array)) != ($n * ($n + 1)) / 2;
        } else {
            return true;
        }
    }

    protected static function getParts(array $data, string $prefix): array
    {
        $result = array();

        foreach ($data as $key => $value) {
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $name = substr($key, strlen($prefix));

                if (!empty($name)) {
                    $result[$name] = $value;
                }
            }
        }

        return $result;
    }
}
