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

namespace PSX\Data\Tests\Visitor;

use PSX\Data\GraphTraverser;
use PSX\Data\Visitor\ValidationVisitor;
use PSX\Uri\Uri;
use PSX\Validate\ValidatorInterface;

/**
 * ValidationVisitorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidationVisitorTest extends VisitorTestCase
{
    public function testTraverse()
    {
        $validator = new SpyValidator();

        $graph = new GraphTraverser();
        $graph->traverse($this->getObject(), new ValidationVisitor($validator));

        $calls  = $validator->getCalls();
        $expect = [
            '/id' => 1,
            '/title' => 'foobar',
            '/active' => true,
            '/disabled' => false,
            '/rating' => 12.45,
            '/age' => null,
            '/date' => new \DateTime('2014-01-01T12:34:47+0100'),
            '/href' => new Uri('http://foo.com'),
            '/person/title' => 'Foo',
            '/category/general/news/technic' => 'Foo',
            '/tags/0' => 'bar',
            '/tags/1' => 'foo',
            '/tags/2' => 'test',
            '/entry/0/title' => 'bar',
            '/entry/1/title' => 'foo',
        ];

        $this->assertEquals($expect, $calls);
    }
}

class SpyValidator implements ValidatorInterface
{
    protected $calls = array();

    public function validate($data)
    {
    }

    public function validateProperty($path, $data)
    {
        $this->calls[$path] = $data;
    }

    public function getCalls()
    {
        return $this->calls;
    }
}
