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

namespace PSX\Data\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use PSX\Data\Transformer\Callback;

/**
 * CallbackTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class CallbackTest extends TestCase
{
    public function testTransform()
    {
        $transformer = new Callback(function ($data) {
            return (object) ['foo' => md5($data)];
        });

        $data = 'some data format';

        $result = $transformer->transform($data);

        $this->assertEquals((object) ['foo' => '791a818e1f5aef6ae38ada7b7317c69a'], $result);
    }
}
