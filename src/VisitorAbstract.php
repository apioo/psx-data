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

namespace PSX\Data;

/**
 * VisitorAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class VisitorAbstract implements VisitorInterface
{
    public function visitObjectStart()
    {
    }

    public function visitObjectEnd()
    {
    }

    public function visitObjectValueStart(string $key, mixed $value)
    {
    }

    public function visitObjectValueEnd()
    {
    }

    public function visitArrayStart()
    {
    }

    public function visitArrayEnd()
    {
    }

    public function visitArrayValueStart(mixed $value)
    {
    }

    public function visitArrayValueEnd()
    {
    }

    public function visitValue(mixed $value)
    {
    }
}
