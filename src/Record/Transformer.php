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

namespace PSX\Data\Record;

use PSX\Data\GraphTraverser;
use PSX\Data\Visitor\ArraySerializeVisitor;
use PSX\Data\Visitor\RecordSerializeVisitor;
use PSX\Data\Visitor\StdClassSerializeVisitor;
use PSX\Record\RecordInterface;

/**
 * Transformer
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Transformer
{
    /**
     * Transforms an arbitrary data structure into a record graph
     *
     * @param mixed $data
     * @param \PSX\Record\RecordInterface $root
     * @return \PSX\Record\RecordInterface
     */
    public static function toRecord($data, RecordInterface $root = null)
    {
        $visitor   = new RecordSerializeVisitor($root);
        $traverser = new GraphTraverser();
        $traverser->traverse($data, $visitor);

        return $visitor->getObject();
    }

    /**
     * Transforms an arbitrary data structure into a stdClass graph
     *
     * @param mixed $data
     * @return \stdClass
     */
    public static function toStdClass($data)
    {
        $visitor   = new StdClassSerializeVisitor();
        $traverser = new GraphTraverser();
        $traverser->traverse($data, $visitor);

        return $visitor->getObject();
    }

    /**
     * Transforms an arbitrary data structure into a stdClass graph
     *
     * @param mixed $data
     * @return array
     */
    public static function toArray($data)
    {
        $visitor   = new ArraySerializeVisitor();
        $traverser = new GraphTraverser();
        $traverser->traverse($data, $visitor);

        return $visitor->getObject();
    }
}
