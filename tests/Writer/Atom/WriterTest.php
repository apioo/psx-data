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

namespace PSX\Data\Tests\Writer\Atom;

use DateTime;
use PSX\Data\Writer\Atom\Writer;

/**
 * WriterTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
    public function testWriter()
    {
        $writer = new Writer('Example Feed', 'urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', new DateTime('2003-12-13T18:30:02Z'));
        $writer->addLink('http://example.org/');
        $writer->addAuthor('John Doe');

        $entry = $writer->createEntry();
        $entry->setTitle('Atom-Powered Robots Run Amok');
        $entry->setId('urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a');
        $entry->setUpdated(new DateTime('2003-12-13T18:30:02Z'));
        $entry->addLink('http://example.org/2003/12/13/atom03');
        $entry->setSummary('Some text.');
        $entry->close();

        $actual   = $writer->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<title>Example Feed</title>
	<id>urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6</id>
	<updated>2003-12-13T18:30:02+00:00</updated>
	<link href="http://example.org/"/>
	<author>
		<name>John Doe</name>
	</author>
	<entry>
		<title>Atom-Powered Robots Run Amok</title>
		<id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
		<updated>2003-12-13T18:30:02+00:00</updated>
		<link href="http://example.org/2003/12/13/atom03"/>
		<summary>Some text.</summary>
	</entry>
</feed>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testComplexWriter()
    {
        $writer = new Writer('dive into mark', 'tag:example.org,2003:3', new DateTime('2005-07-31T12:29:29Z'));
        $writer->setSubTitle('html', 'A <em>lot</em> of effort went into making this effortless');
        $writer->addLink('http://example.org/', 'alternate', 'text/html', 'en', 'foo');
        $writer->addLink('http://example.org/feed.atom', 'self', 'application/atom+xml');
        $writer->setRights('Copyright (c) 2003, Mark Pilgrim');
        $writer->setGenerator('Example Toolkit', 'http://www.example.com/', '1.0');
        $writer->addCategory('foo', 'urn:foo:bar', 'foo@bar.com');

        $entry = $writer->createEntry();
        $entry->setTitle('Atom draft-07 snapshot');
        $entry->setId('tag:example.org,2003:3.2397');
        $entry->setUpdated(new DateTime('2005-07-31T12:29:29Z'));
        $entry->addLink('http://example.org/2005/04/02/atom', 'alternate', 'text/html');
        $entry->addLink('http://example.org/audio/ph34r_my_podcast.mp3', 'enclosure', 'audio/mpeg', null, null, 1337);
        $entry->setPublished(new DateTime('2003-12-13T08:29:29-04:00'));
        $entry->addAuthor('Mark Pilgrim', 'http://example.org/', 'f8dy@example.com');
        $entry->addContributor('Sam Ruby');
        $entry->addContributor('Joe Gregorio');
        $entry->setContent('<div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div>', 'xhtml');
        $entry->close();

        $actual   = $writer->toString();
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<title>dive into mark</title>
	<id>tag:example.org,2003:3</id>
	<updated>2005-07-31T12:29:29+00:00</updated>
	<subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
	<link rel="alternate" type="text/html" hreflang="en" title="foo" href="http://example.org/"/>
	<link rel="self" type="application/atom+xml" href="http://example.org/feed.atom"/>
	<rights>Copyright (c) 2003, Mark Pilgrim</rights>
	<generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
	<category label="foo@bar.com" scheme="urn:foo:bar" term="foo"/>
	<entry>
		<title>Atom draft-07 snapshot</title>
		<id>tag:example.org,2003:3.2397</id>
		<updated>2005-07-31T12:29:29+00:00</updated>
		<link rel="alternate" type="text/html" href="http://example.org/2005/04/02/atom"/>
		<link rel="enclosure" type="audio/mpeg" length="1337" href="http://example.org/audio/ph34r_my_podcast.mp3"/>
		<published>2003-12-13T08:29:29-04:00</published>
		<author>
			<name>Mark Pilgrim</name>
			<uri>http://example.org/</uri>
			<email>f8dy@example.com</email>
		</author>
		<contributor>
			<name>Sam Ruby</name>
		</contributor>
		<contributor>
			<name>Joe Gregorio</name>
		</contributor>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div>
		</content>
	</entry>
</feed>
XML;

        $this->assertXmlStringEqualsXmlString($expected, $actual);
    }

    public function testGetWriter()
    {
        $writer = new Writer('dive into mark', 'tag:example.org,2003:3', new DateTime('2005-07-31T12:29:29Z'));

        $this->assertInstanceOf('XMLWriter', $writer->getWriter());
    }

    public function testGetCustomWriter()
    {
        $handle = $this->createMock('XMLWriter');
        $writer = new Writer('dive into mark', 'tag:example.org,2003:3', new DateTime('2005-07-31T12:29:29Z'), $handle);

        $this->assertTrue($handle === $writer->getWriter());
    }

    public function testLink()
    {
        $html = Writer::link('foo > bar', 'http://foo.com');

        $this->assertEquals('<link rel="alternate" type="application/atom+xml" title="foo &gt; bar" href="http://foo.com" />', $html);
    }
}
