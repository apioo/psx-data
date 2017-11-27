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

namespace PSX\Data\Tests\Exporter;

use Doctrine\Common\Annotations\AnnotationReader;
use PSX\Data\Exporter\Popo;
use PSX\Record\Record;

/**
 * PopoTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PopoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider exportProvider
     */
    public function testExport($data, $expect)
    {
        $exporter = $this->getExporter();
        $record   = $exporter->export($data);

        $this->assertEquals($expect, $record);
    }

    public function exportProvider()
    {
        return [
            [[], []],
            [['foo'], ['foo']],
            [['foo', 'bar'], ['foo', 'bar']],
            [['foo' => 'bar'], ['foo' => 'bar']],
            [(object) ['foo' => 'bar'], (object) ['foo' => 'bar']],
            [Record::fromArray(['foo' => 'bar']), Record::fromArray(['foo' => 'bar'])],
            [new Location(12, 34), Record::fromArray(['lat' => 12, 'long' => 34], 'Location')],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Data must be an object
     */
    public function testExportInvalid()
    {
        $exporter = $this->getExporter();
        $exporter->export('foo');
    }

    private function getExporter()
    {
        $reader   = new AnnotationReader();
        $exporter = new Popo($reader);

        return $exporter;
    }
}
