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

namespace PSX\Data\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use PSX\Record\Record;

/**
 * WriterTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class WriterTestCase extends TestCase
{
    /**
     * @return Record<mixed>
     */
    public function getRecord(): Record
    {
        /** @var Record<mixed> $record */
        $record = new Record();
        $record->put('id', 1);
        $record->put('author', 'foo');
        $record->put('title', 'bar');
        $record->put('content', 'foobar');
        $record->put('date', new DateTime('2012-03-11 13:37:21'));

        return $record;
    }

    /**
     * @return Record<mixed>
     */
    public function getCollection(): Record
    {
        $entries = array();

        /** @var Record<mixed> $record */
        $record = new Record();
        $record->put('id', 1);
        $record->put('author', 'foo');
        $record->put('title', 'bar');
        $record->put('content', 'foobar');
        $record->put('date', new DateTime('2012-03-11 13:37:21'));

        $entries[] = $record;

        /** @var Record<mixed> $record */
        $record = new Record();
        $record->put('id', 2);
        $record->put('author', 'foo');
        $record->put('title', 'bar');
        $record->put('content', 'foobar');
        $record->put('date', new DateTime('2012-03-11 13:37:21'));

        $entries[] = $record;

        /** @var Record<mixed> $record */
        $record = new Record();
        $record->put('totalResults', 2);
        $record->put('startIndex', 0);
        $record->put('itemsPerPage', 8);
        $record->put('entry', $entries);

        return $record;
    }

    public function getComplex(): Record
    {
        /** @var Record<mixed> $actor */
        $actor = new Record();
        $actor->put('id', 'tag:example.org,2011:martin');
        $actor->put('objectType', 'person');
        $actor->put('displayName', 'Martin Smith');
        $actor->put('url', 'http://example.org/martin');

        /** @var Record<mixed> $object */
        $object = new Record();
        $object->put('id', 'tag:example.org,2011:abc123/xyz');
        $object->put('url', 'http://example.org/blog/2011/02/entry');

        /** @var Record<mixed> $target */
        $target = new Record();
        $target->put('id', 'tag:example.org,2011:abc123');
        $target->put('objectType', 'blog');
        $target->put('displayName', 'Martin\'s Blog');
        $target->put('url', 'http://example.org/blog/');

        /** @var Record<mixed> $activity */
        $activity = new Record();
        $activity->put('verb', 'post');
        $activity->put('actor', $actor);
        $activity->put('object', $object);
        $activity->put('target', $target);
        $activity->put('published', new DateTime('2011-02-10T15:04:55Z'));

        return $activity;
    }

    public function getEmpty(): Record
    {
        return new Record([]);
    }

    /**
     * @return array<Record<mixed>>
     */
    public function getArray(): array
    {
        $entries = array();

        /** @var Record<mixed> $record */
        $record = new Record();
        $record->put('id', 1);
        $record->put('author', 'foo');
        $record->put('title', 'bar');
        $record->put('content', 'foobar');
        $record->put('date', new DateTime('2012-03-11 13:37:21'));

        $entries[] = $record;

        /** @var Record<mixed> $record */
        $record = new Record();
        $record->put('id', 2);
        $record->put('author', 'foo');
        $record->put('title', 'bar');
        $record->put('content', 'foobar');
        $record->put('date', new DateTime('2012-03-11 13:37:21'));

        $entries[] = $record;

        return $entries;
    }

    /**
     * @return array<string>
     */
    public function getArrayScalar(): array
    {
        return ['foo', 'bar'];
    }

    public function getScalar(): string
    {
        return 'foobar';
    }

    abstract public function testWriteRecord();

    abstract public function testWriteCollection();

    abstract public function testWriteEmpty();

    abstract public function testWriteArray();

    abstract public function testWriteArrayScalar();

    abstract public function testWriteScalar();
}
