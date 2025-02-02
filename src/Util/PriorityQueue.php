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

namespace PSX\Data\Util;

use Countable;
use IteratorAggregate;
use SplPriorityQueue;

/**
 * A priority queue which you can iterate multiple times. The SplPriorityQueue
 * removes an element after traversing
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 *
 * @implements IteratorAggregate<mixed>
 */
class PriorityQueue implements IteratorAggregate, Countable
{
    private SplPriorityQueue $queue;

    public function __construct()
    {
        $this->queue = new SplPriorityQueue();
    }

    public function insert($value, $priority): void
    {
        $this->queue->insert($value, $priority);
    }

    public function count(): int
    {
        return $this->queue->count();
    }

    public function getIterator(): \Traversable
    {
        return clone $this->queue;
    }
}
