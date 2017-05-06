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

use PSX\Json\Pointer;
use PSX\Validate\FilterInterface;

/**
 * Accessor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Accessor
{
    public static function get($source, $path, array $filters = array())
    {
        $pointer = new Pointer('/' . ltrim($path, '/'));
        $value   = $pointer->evaluate($source);

        foreach ($filters as $filter) {
            $return  = null;
            $error   = null;
            if ($filter instanceof FilterInterface) {
                $return = $filter->apply($value);
                $error  = $filter->getErrorMessage();
            } elseif ($filter instanceof \Closure) {
                $return = $filter($value);
            }

            if ($return === false) {
                if (empty($error)) {
                    $error = '%s contains an invalid value';
                }

                throw new \InvalidArgumentException(sprintf($error, $path));
            } elseif ($return === true) {
            } else {
                $value = $return;
            }
        }

        return $value;
    }
}
