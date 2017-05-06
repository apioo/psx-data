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

namespace PSX\Data\Tests;

use Doctrine\Common\Annotations\SimpleAnnotationReader;
use PSX\Data\Configuration;
use PSX\Data\Processor;
use PSX\Schema\SchemaManager;
use PSX\Schema\Visitor\OutgoingVisitor;

/**
 * ProcessorTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ProcessorTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PSX\Data\Processor
     */
    protected $processor;

    protected function setUp()
    {
        $reader = new SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');

        $processor = new Processor(Configuration::createDefault($reader, new SchemaManager($reader)));

        $this->processor = $processor;
    }
}
