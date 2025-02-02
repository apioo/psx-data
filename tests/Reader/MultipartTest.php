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

namespace PSX\Data\Tests\Reader;

use PHPUnit\Framework\TestCase;
use PSX\Data\Multipart\Body;
use PSX\Data\Reader\Multipart;

/**
 * MultipartTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class MultipartTest extends TestCase
{
    public function testRead()
    {
        $files = [];
        $files['foo'] = [
            'name' => 'upload.txt',
            'type' => 'text/plain',
            'size' => 8,
            'tmp_name' => '/tmp/upload.txt',
            'error' => UPLOAD_ERR_OK,
        ];

        $post = ['bar' => 'foo'];

        $reader = new Multipart($files, $post);
        $data = $reader->read('');

        $this->assertInstanceOf(Body::class, $data);

        $actual = json_encode($reader->read(''), JSON_PRETTY_PRINT);

        $expect = <<<JSON
{
    "foo": {
        "name": "upload.txt",
        "type": "text\/plain",
        "size": 8,
        "tmp_name": "\/tmp\/upload.txt",
        "error": 0
    },
    "bar": "foo"
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testReadNoFiles()
    {
        $files = [];
        $files['foo'] = [];

        $post = ['bar' => 'foo'];

        $reader = new Multipart($files, $post);
        $data = $reader->read('');

        $this->assertInstanceOf(\stdClass::class, $data);

        $actual = json_encode($data, JSON_PRETTY_PRINT);

        $expect = <<<JSON
{
    "bar": "foo"
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testReadEmpty()
    {
        $reader = new Multipart();
        $file   = $reader->read('');

        $this->assertEmpty($file);
    }
}
