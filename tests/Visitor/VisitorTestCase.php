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

namespace PSX\Data\Tests\Visitor;

use DateTime;
use PHPUnit\Framework\TestCase;
use PSX\DateTime\LocalDateTime;
use PSX\Record\Record;
use PSX\Uri\Uri;

/**
 * ImporterTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class VisitorTestCase extends TestCase
{
    protected function getObject(): object
    {
        return (object) [
            'id' => 1,
            'title' => 'foobar',
            'active' => true,
            'disabled' => false,
            'rating' => 12.45,
            'age' => null,
            'date' => LocalDateTime::parse('2014-01-01T12:34:47+01:00'),
            'href' => Uri::parse('http://foo.com'),
            'person' => new Record([
                'title' => 'Foo',
            ]),
            'category' => new Record([
                'general' => new Record([
                    'news' => new Record([
                        'technic' => 'Foo',
                    ]),
                ]),
            ]),
            'tags' => ['bar', 'foo', 'test'],
            'entry' => [
                new Record([
                    'title' => 'bar'
                ]),
                new Record([
                    'title' => 'foo'
                ]),
            ],
        ];
    }

    protected function getArray(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'foobar',
                'active' => true,
                'disabled' => false,
                'rating' => 12.45,
            ],
            [
                'id' => 2,
                'title' => 'foo',
                'active' => false,
                'disabled' => false,
                'rating' => 12.45,
            ]
        ];
    }

    protected function getArrayNested(): array
    {
        return [
            ['foo', 'bar']
        ];
    }

    protected function getArrayScalar(): array
    {
        return ['foo', 'bar'];
    }
}
