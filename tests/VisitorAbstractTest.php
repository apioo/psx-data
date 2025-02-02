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

use PHPUnit\Framework\TestCase;
use PSX\Data\VisitorAbstract;
use PSX\Data\VisitorInterface;

/**
 * VisitorAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class VisitorAbstractTest extends TestCase
{
    public function testVisitor()
    {
        // the abstract visitor exists so that visitor implementations must not
        // implement every method. We simply test that we can call each method
        // of the interface

        $visitor = new FooVisitor();

        $visitor->visitObjectStart('foo');
        $visitor->visitObjectEnd();
        $visitor->visitObjectValueStart('bar', 'test');
        $visitor->visitObjectValueEnd();
        $visitor->visitArrayStart();
        $visitor->visitArrayEnd();
        $visitor->visitArrayValueStart('foo');
        $visitor->visitArrayValueEnd();
        $visitor->visitValue('foo');

        $this->assertInstanceOf(VisitorInterface::class, $visitor);
    }
}

class FooVisitor extends VisitorAbstract
{
}
