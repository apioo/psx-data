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

namespace PSX\Data\Tests\Transformer;

use PHPUnit\Framework\TestCase;
use PSX\Data\Exception\InvalidDataException;
use PSX\Data\Transformer\Soap;

/**
 * SoapTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class SoapTest extends TestCase
{
    public function testTransform()
    {
        $body = <<<INPUT
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<test xmlns="http://phpsx.org/2014/data">
			<foo>bar</foo>
			<bar>blub</bar>
			<bar>bla</bar>
			<test>
				<foo>bar</foo>
			</test>
		</test>
	</soap:Body>
</soap:Envelope>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap();
        $transformer->setNamespace('http://phpsx.org/2014/data');

        $expect = new \stdClass();
        $expect->foo = 'bar';
        $expect->bar = ['blub', 'bla'];
        $expect->test = new \stdClass();
        $expect->test->foo = 'bar';

        $data = $transformer->transform($dom);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals($expect, $data);
    }

    public function testNoEnvelope()
    {
        $this->expectException(InvalidDataException::class);

        $body = <<<INPUT
<test xmlns="http://phpsx.org/2014/data">
	<foo>bar</foo>
	<bar>blub</bar>
	<bar>bla</bar>
	<test>
		<foo>bar</foo>
	</test>
</test>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap('http://phpsx.org/2014/data');
        $transformer->transform($dom);
    }

    public function testEmptyBody()
    {
        $body = <<<INPUT
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
	</soap:Body>
</soap:Envelope>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap('http://phpsx.org/2014/data');

        $expect = new \stdClass();

        $data = $transformer->transform($dom);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals($expect, $data);
    }

    public function testBodyWrongNamespace()
    {
        $this->expectException(InvalidDataException::class);

        $body = <<<INPUT
<soap:Envelope xmlns:soap="http://www.w3.org/2001/12/soap-envelope">
	<soap:Body>
	</soap:Body>
</soap:Envelope>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap('http://phpsx.org/2014/data');
        $transformer->transform($dom);
    }

    public function testInvalidData()
    {
        $this->expectException(InvalidDataException::class);

        $transformer = new Soap('http://phpsx.org/2014/data');
        $transformer->transform(array());
    }
}
