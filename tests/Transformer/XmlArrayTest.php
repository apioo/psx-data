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

namespace PSX\Data\Tests\Transformer;

use PSX\Data\Transformer\XmlArray;
use PSX\Rss;

/**
 * XmlArrayTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $body = <<<INPUT
<test>
	<empty />
	<empty_2></empty_2>
	<foo>bar</foo>
	<bar>blub</bar>
	<bar>bla</bar>
	<test>
		<foo>bar</foo>
	</test>
	<foooo>
		<test>
			<title>blub</title>
		</test>
		<bar>
			<title>foo</title>
		</bar>
	</foooo>
	<item>
		<title>foo</title>
		<text>bar</text>
	</item>
	<item>
		<title>foo</title>
		<text>bar</text>
	</item>
	<item>
		<title>foo</title>
		<text>bar</text>
	</item>
	<item>
		<title>foo</title>
		<text>bar</text>
	</item>
</test>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new XmlArray();

        $expect = new \stdClass();
        $expect->empty = '';
        $expect->empty_2 = '';
        $expect->foo = 'bar';
        $expect->bar = ['blub', 'bla'];
        $expect->test = new \stdClass();
        $expect->test->foo = 'bar';
        $expect->foooo = new \stdClass();
        $expect->foooo->test = new \stdClass();
        $expect->foooo->test->title = 'blub';
        $expect->foooo->bar = new \stdClass();
        $expect->foooo->bar->title = 'foo';
        $expect->item = [];
        $expect->item[0] = new \stdClass();
        $expect->item[0]->title = 'foo';
        $expect->item[0]->text = 'bar';
        $expect->item[1] = new \stdClass();
        $expect->item[1]->title = 'foo';
        $expect->item[1]->text = 'bar';
        $expect->item[2] = new \stdClass();
        $expect->item[2]->title = 'foo';
        $expect->item[2]->text = 'bar';
        $expect->item[3] = new \stdClass();
        $expect->item[3]->title = 'foo';
        $expect->item[3]->text = 'bar';

        $data = $transformer->transform($dom);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals($expect, $data);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidData()
    {
        $transformer = new XmlArray();
        $transformer->transform(array());
    }

    public function testTransformNamespace()
    {
        $body = <<<INPUT
<foo:test xmlns:foo="http://foo.com" xmlns:bar="http://bar.com">
	<foo:foo>bar</foo:foo>
	<foo:bar>blub</foo:bar>
	<bar:bar>bla</bar:bar>
	<foo:test>
		<foo:foo>bar</foo:foo>
	</foo:test>
	<foo:foooo>
		<bar:bar>bar</bar:bar>
		<bar:foo>bar</bar:foo>
	</foo:foooo>
</foo:test>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new XmlArray();
        $transformer->setNamespace('http://foo.com');

        $expect = new \stdClass();
        $expect->foo = 'bar';
        $expect->bar = 'blub';
        $expect->test = new \stdClass();
        $expect->test->foo = 'bar';
        $expect->foooo = $dom->getElementsByTagName('foooo')->item(0);

        $data = $transformer->transform($dom);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals($expect, $data);
    }
}
