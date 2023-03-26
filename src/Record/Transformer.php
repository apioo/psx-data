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

namespace PSX\Data\Record;

use PSX\Data\GraphTraverser;
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
     */
    public static function toRecord(mixed $data, RecordInterface $root = null): RecordInterface
    {
        $visitor = new RecordSerializeVisitor($root);
        (new GraphTraverser())->traverse($data, $visitor);

        $result = $visitor->getObject();
        if ($result instanceof RecordInterface) {
            return $result;
        } else {
            throw new \RuntimeException('Could not transform to record');
        }
    }

    /**
     * Transforms an arbitrary data structure into a stdClass graph
     */
    public static function toStdClass(mixed $data): \stdClass
    {
        $visitor = new StdClassSerializeVisitor();
        (new GraphTraverser())->traverse($data, $visitor);

        $result = $visitor->getObject();
        if ($result instanceof \stdClass) {
            return $result;
        } else {
            throw new \RuntimeException('Could not transform to stdClass');
        }
    }
}
