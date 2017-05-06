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

namespace PSX\Data\Tests\Record;

use PSX\Data\Record\Transformer;
use PSX\Record\Record;

/**
 * TransformerTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testToRecord()
    {
        $data = new \stdClass();
        $data->id = 1;
        $data->foo = 'bar';

        $result = Transformer::toRecord($data);

        $expect = Record::fromArray(['id' => 1, 'foo' => 'bar']);

        $this->assertInstanceOf('PSX\Record\RecordInterface', $result);
        $this->assertEquals($expect, $result);
    }

    public function testToStdClass()
    {
        $data = Record::fromArray(['id' => 1, 'foo' => 'bar']);

        $result = Transformer::toStdClass($data);

        $expect = new \stdClass();
        $expect->id = 1;
        $expect->foo = 'bar';

        $this->assertInstanceOf('stdClass', $result);
        $this->assertEquals($expect, $result);
    }
}
