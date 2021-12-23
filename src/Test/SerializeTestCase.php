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

namespace PSX\Data\Test;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PHPUnit\Framework\TestCase;
use PSX\Data\Configuration;
use PSX\Data\Exception\InvalidDataException;
use PSX\Data\Payload;
use PSX\Data\Processor;
use PSX\Schema\Exception\ValidationException;
use PSX\Schema\SchemaManager;

/**
 * SerializeTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class SerializeTestCase extends TestCase
{
    /**
     * Checks whether the records can be serialzed to the content format and the
     * content format can be serialized to the record without loosing data
     *
     * @throws InvalidDataException
     * @throws ValidationException
     */
    protected function assertRecordEqualsContent(object $record, string $content): void
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

        return new Processor(Configuration::createDefault(new SchemaManager()));
    }
}
