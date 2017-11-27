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

namespace PSX\Data\Exporter;

use Doctrine\Common\Annotations\Reader;
use PSX\Data\ExporterInterface;
use PSX\Data\GraphTraverser;
use PSX\Schema\Parser\Popo\Dumper;

/**
 * Exports an arbitrary object to a record
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Popo implements ExporterInterface
{
    /**
     * @var \PSX\Schema\Parser\Popo\Dumper
     */
    protected $dumper;

    public function __construct(Reader $reader)
    {
        $this->dumper = new Dumper($reader);
    }

    public function export($data)
    {
        if (GraphTraverser::isObject($data)) {
            return $data;
        } elseif (GraphTraverser::isArray($data)) {
            return $data;
        } elseif (is_object($data)) {
            return $this->dumper->dump($data);
        } else {
            throw new \InvalidArgumentException('Data must be an object');
        }
    }
}
