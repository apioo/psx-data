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
use PSX\Data\Visitor\HtmlWriterVisitor;

/**
 * HtmlWriterVisitorTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class HtmlWriterVisitorTest extends VisitorTestCase
{
    public function testTraverseObject()
    {
        $visitor = new HtmlWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getObject(), $visitor);

        $this->assertXmlStringEqualsXmlString($this->getExpectedObject(), $visitor->getOutput());
    }

    public function testTraverseArray()
    {
        $visitor = new HtmlWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArray(), $visitor);

        $this->assertXmlStringEqualsXmlString($this->getExpectedArray(), $visitor->getOutput());
    }

    public function testTraverseArrayNested()
    {
        $visitor = new HtmlWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayNested(), $visitor);

        $this->assertXmlStringEqualsXmlString($this->getExpectedArrayNested(), $visitor->getOutput());
    }

    public function testTraverseArrayScalar()
    {
        $visitor = new HtmlWriterVisitor();

        $graph = new GraphTraverser();
        $graph->traverse($this->getArrayScalar(), $visitor);

        $this->assertXmlStringEqualsXmlString($this->getExpectedArrayScalar(), $visitor->getOutput());
    }

    protected function getExpectedObject()
    {
        return <<<HTML
<dl data-name="record">
    <dt>id</dt>
    <dd>1</dd>
    <dt>title</dt>
    <dd>foobar</dd>
    <dt>active</dt>
    <dd>true</dd>
    <dt>disabled</dt>
    <dd>false</dd>
    <dt>rating</dt>
    <dd>12.45</dd>
    <dt>age</dt>
    <dd/>
    <dt>date</dt>
    <dd>2014-01-01T12:34:47+01:00</dd>
    <dt>href</dt>
    <dd>http://foo.com</dd>
    <dt>person</dt>
    <dd>
        <dl data-name="person">
            <dt>title</dt>
            <dd>Foo</dd>
        </dl>
    </dd>
    <dt>category</dt>
    <dd>
        <dl data-name="category">
            <dt>general</dt>
            <dd>
                <dl data-name="category">
                    <dt>news</dt>
                    <dd>
                        <dl data-name="category">
                            <dt>technic</dt>
                            <dd>Foo</dd>
                        </dl>
                    </dd>
                </dl>
            </dd>
        </dl>
    </dd>
    <dt>tags</dt>
    <dd>
        <ul>
            <li>bar</li>
            <li>foo</li>
            <li>test</li>
        </ul>
    </dd>
    <dt>entry</dt>
    <dd>
        <ul>
            <li>
                <dl data-name="entry">
                    <dt>title</dt>
                    <dd>bar</dd>
                </dl>
            </li>
            <li>
                <dl data-name="entry">
                    <dt>title</dt>
                    <dd>foo</dd>
                </dl>
            </li>
        </ul>
    </dd>
</dl>
HTML;
    }

    protected function getExpectedArray()
    {
        return <<<HTML
<?xml version="1.0"?>
<ul>
  <li>
    <dl data-name="record">
      <dt>id</dt>
      <dd>1</dd>
      <dt>title</dt>
      <dd>foobar</dd>
      <dt>active</dt>
      <dd>true</dd>
      <dt>disabled</dt>
      <dd>false</dd>
      <dt>rating</dt>
      <dd>12.45</dd>
    </dl>
  </li>
  <li>
    <dl data-name="record">
      <dt>id</dt>
      <dd>2</dd>
      <dt>title</dt>
      <dd>foo</dd>
      <dt>active</dt>
      <dd>false</dd>
      <dt>disabled</dt>
      <dd>false</dd>
      <dt>rating</dt>
      <dd>12.45</dd>
    </dl>
  </li>
</ul>
HTML;
    }

    protected function getExpectedArrayNested()
    {
        return <<<HTML
<?xml version="1.0"?>
<ul>
  <li>
    <ul>
      <li>foo</li>
      <li>bar</li>
    </ul>
  </li>
</ul>
HTML;
    }

    protected function getExpectedArrayScalar()
    {
        return <<<HTML
<?xml version="1.0"?>
<ul>
  <li>foo</li>
  <li>bar</li>
</ul>
HTML;
    }
}
