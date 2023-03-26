<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace PSX\Data\Tests\Reader;

use PHPUnit\Framework\TestCase;
use PSX\Data\Reader\Json;

/**
 * JsonTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class JsonTest extends TestCase
{
    public function testRead()
    {
        $body = <<<INPUT
{
    "foo": "bar",
    "bar": [
        "blub",
        "bla"
    ],
    "test": {
        "foo": "bar"
    },
    "item": {
        "foo": {
            "bar": {
                "title": "foo"
            }
        }
    },
    "items": {
        "item": [
            {
                "title": "foo",
                "text": "bar"
            },
            {
                "title": "foo",
                "text": "bar"
            }
        ]
    }
}
INPUT;

        $reader = new Json();
        $actual = json_encode($reader->read($body), JSON_PRETTY_PRINT);

        $expect = $body;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testReadEmpty()
    {
        $reader = new Json();
        $json   = $reader->read('');

        $this->assertNull($json);
    }
}
