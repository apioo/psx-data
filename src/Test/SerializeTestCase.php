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

namespace PSX\Data\Test;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Data\Configuration;
use PSX\Data\Payload;
use PSX\Data\Processor;
use PSX\Schema\SchemaManager;

/**
 * SerializeTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SerializeTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Checks whether the records can be serialzed to the content format and the
     * content format can be serialized to the record without loosing data
     *
     * @param object $record
     * @param string $content
     */
    protected function assertRecordEqualsContent($record, $content)
    {
        // serialize the record
        $response = $this->getProcessor()->write(Payload::json($record));

        // check whether the response is the same as the content
        $this->assertJsonStringEqualsJsonString($content, $response);

        // create a new record of the same class and import the content
        $newRecord = $this->getProcessor()->read(get_class($record), Payload::json($content));

        // get response
        $newResponse = $this->getProcessor()->write(Payload::json($newRecord));

        // check whether the newResponse is the same as the content
        $this->assertJsonStringEqualsJsonString($content, $newResponse);

        // check whether the newResponse is the same as the response
        $this->assertJsonStringEqualsJsonString($response, $newResponse);
    }

    protected function getProcessor()
    {
        static $processor;

        if ($processor) {
            return $processor;
        }

        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $processor = new Processor(Configuration::createDefault($reader, new SchemaManager($reader)));

        return $processor;
    }
}
