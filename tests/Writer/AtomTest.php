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

namespace PSX\Data\Tests\Writer;

use PSX\Data\Tests\WriterTestCase;
use PSX\Data\Writer\Atom;
use PSX\DateTime\DateTime;
use PSX\Http\MediaType;
use PSX\Model\Atom\Atom as AtomRecord;
use PSX\Model\Atom\Category;
use PSX\Model\Atom\Entry;
use PSX\Model\Atom\Generator;
use PSX\Model\Atom\Link;
use PSX\Model\Atom\Person;
use PSX\Model\Atom\Text;

/**
 * AtomTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AtomTest extends WriterTestCase
{
    public function testWriteFeed()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getAtomRecord());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
 <title>dive into mark</title>
 <id>tag:example.org,2003:3</id>
 <updated>2005-07-31T12:29:29+00:00</updated>
 <subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
 <link href="http://example.org/" rel="alternate" type="text/html" hreflang="en"/>
 <link href="http://example.org/feed.atom" rel="self" type="application/atom+xml"/>
 <rights>Copyright (c) 2003, Mark Pilgrim</rights>
 <generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
 <author>
  <name>Mark Pilgrim</name>
  <uri>http://example.org/</uri>
  <email>f8dy@example.com</email>
 </author>
 <category term="news"/>
 <contributor>
  <name>Sam Ruby</name>
 </contributor>
 <icon>http://localhost.com/icon.png</icon>
 <logo>http://localhost.com/logo.png</logo>
 <entry>
  <id>tag:example.org,2003:3.2397</id>
  <title>Atom draft-07 snapshot</title>
  <updated>2005-07-31T12:29:29+00:00</updated>
  <published>2003-12-13T08:29:29-04:00</published>
  <link href="http://example.org/2005/04/02/atom" rel="alternate" type="text/html"/>
  <link href="http://example.org/audio/ph34r_my_podcast.mp3" rel="enclosure" type="audio/mpeg" length="1337"/>
  <rights>Copyright (c) 2003, Mark Pilgrim</rights>
  <author>
   <name>Mark Pilgrim</name>
   <uri>http://example.org/</uri>
   <email>f8dy@example.com</email>
  </author>
  <category term="news"/>
  <contributor>
   <name>Sam Ruby</name>
  </contributor>
  <contributor>
   <name>Joe Gregorio</name>
  </contributor>
  <content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div></content>
  <summary type="text">foobar</summary>
  <source>
   <title>dive into mark</title>
   <id>tag:example.org,2003:3</id>
   <updated>2005-07-31T12:29:29+00:00</updated>
   <subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
   <link href="http://example.org/" rel="alternate" type="text/html" hreflang="en"/>
   <link href="http://example.org/feed.atom" rel="self" type="application/atom+xml"/>
   <rights>Copyright (c) 2003, Mark Pilgrim</rights>
   <generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
  </source>
 </entry>
</feed>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteEntry()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getEntryRecord());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <id>tag:example.org,2003:3.2397</id>
 <title>Atom draft-07 snapshot</title>
 <updated>2005-07-31T12:29:29+00:00</updated>
 <published>2003-12-13T08:29:29-04:00</published>
 <link href="http://example.org/2005/04/02/atom" rel="alternate" type="text/html"/>
 <link href="http://example.org/audio/ph34r_my_podcast.mp3" rel="enclosure" type="audio/mpeg" length="1337"/>
 <rights>Copyright (c) 2003, Mark Pilgrim</rights>
 <author>
  <name>Mark Pilgrim</name>
  <uri>http://example.org/</uri>
  <email>f8dy@example.com</email>
 </author>
 <category term="news"/>
 <contributor>
  <name>Sam Ruby</name>
 </contributor>
 <contributor>
  <name>Joe Gregorio</name>
 </contributor>
 <content type="xhtml"><div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div></content>
 <summary type="text">foobar</summary>
 <source>
  <title>dive into mark</title>
  <id>tag:example.org,2003:3</id>
  <updated>2005-07-31T12:29:29+00:00</updated>
  <subtitle type="html">A &lt;em&gt;lot&lt;/em&gt; of effort went into making this effortless</subtitle>
  <link href="http://example.org/" rel="alternate" type="text/html" hreflang="en"/>
  <link href="http://example.org/feed.atom" rel="self" type="application/atom+xml"/>
  <rights>Copyright (c) 2003, Mark Pilgrim</rights>
  <generator uri="http://www.example.com/" version="1.0">Example Toolkit</generator>
 </source>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    protected function getAtomRecord()
    {
        $subTitle = new Text();
        $subTitle->setContent('A <em>lot</em> of effort went into making this effortless');
        $subTitle->setType('html');

        $link1 = new Link();
        $link1->setHref('http://example.org/');
        $link1->setRel('alternate');
        $link1->setType('text/html');
        $link1->setHreflang('en');

        $link2 = new Link();
        $link2->setHref('http://example.org/feed.atom');
        $link2->setRel('self');
        $link2->setType('application/atom+xml');

        $generator = new Generator();
        $generator->setText('Example Toolkit');
        $generator->setUri('http://www.example.com/');
        $generator->setVersion('1.0');

        $author = new Person();
        $author->setName('Mark Pilgrim');
        $author->setUri('http://example.org/');
        $author->setEmail('f8dy@example.com');

        $contributor = new Person();
        $contributor->setName('Sam Ruby');

        $category = new Category();
        $category->setTerm('news');

        $atom = new AtomRecord();
        $atom->setTitle('dive into mark');
        $atom->setSubTitle($subTitle);
        $atom->setUpdated(new DateTime('2005-07-31T12:29:29Z'));
        $atom->setId('tag:example.org,2003:3');
        $atom->setLink([$link1, $link2]);
        $atom->setRights('Copyright (c) 2003, Mark Pilgrim');
        $atom->setGenerator($generator);
        $atom->setAuthor([$author]);
        $atom->setContributor([$contributor]);
        $atom->setCategory([$category]);
        $atom->setIcon('http://localhost.com/icon.png');
        $atom->setLogo('http://localhost.com/logo.png');
        $atom->setEntry([$this->getEntryRecord()]);

        return $atom;
    }

    protected function getEntryRecord()
    {
        $link1 = new Link();
        $link1->setHref('http://example.org/2005/04/02/atom');
        $link1->setRel('alternate');
        $link1->setType('text/html');

        $link2 = new Link();
        $link2->setHref('http://example.org/audio/ph34r_my_podcast.mp3');
        $link2->setRel('enclosure');
        $link2->setType('audio/mpeg');
        $link2->setLength(1337);

        $author = new Person();
        $author->setName('Mark Pilgrim');
        $author->setUri('http://example.org/');
        $author->setEmail('f8dy@example.com');

        $contributor1 = new Person();
        $contributor1->setName('Sam Ruby');

        $contributor2 = new Person();
        $contributor2->setName('Joe Gregorio');

        $category = new Category();
        $category->setTerm('news');

        $content = new Text();
        $content->setContent('<div xmlns="http://www.w3.org/1999/xhtml"><p><i>[Update: The Atom draft is finished.]</i></p></div>');
        $content->setType('xhtml');

        $summary = new Text();
        $summary->setContent('foobar');
        $summary->setType('text');

        $entry = new Entry();
        $entry->setTitle('Atom draft-07 snapshot');
        $entry->setLink([$link1, $link2]);
        $entry->setId('tag:example.org,2003:3.2397');
        $entry->setUpdated(new DateTime('2005-07-31T12:29:29'));
        $entry->setPublished(new DateTime('2003-12-13T08:29:29-04:00'));
        $entry->setAuthor([$author]);
        $entry->setContributor([$contributor1, $contributor2]);
        $entry->setCategory([$category]);
        $entry->setContent($content);
        $entry->setSummary($summary);
        $entry->setRights('Copyright (c) 2003, Mark Pilgrim');

        $subTitle = new Text();
        $subTitle->setContent('A <em>lot</em> of effort went into making this effortless');
        $subTitle->setType('html');

        $link1 = new Link();
        $link1->setHref('http://example.org/');
        $link1->setRel('alternate');
        $link1->setType('text/html');
        $link1->setHreflang('en');

        $link2 = new Link();
        $link2->setHref('http://example.org/feed.atom');
        $link2->setRel('self');
        $link2->setType('application/atom+xml');

        $generator = new Generator();
        $generator->setText('Example Toolkit');
        $generator->setUri('http://www.example.com/');
        $generator->setVersion('1.0');

        $atom = new AtomRecord();
        $atom->setTitle('dive into mark');
        $atom->setSubTitle($subTitle);
        $atom->setUpdated(new DateTime('2005-07-31T12:29:29Z'));
        $atom->setId('tag:example.org,2003:3');
        $atom->setLink([$link1, $link2]);
        $atom->setRights('Copyright (c) 2003, Mark Pilgrim');
        $atom->setGenerator($generator);

        $entry->setSource($atom);

        return $entry;
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Atom();

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/atom+xml')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('application/xml')));
    }

    public function testGetContentType()
    {
        $writer = new Atom();

        $this->assertEquals('application/atom+xml', $writer->getContentType());
    }

    public function testWriteRecord()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getRecord());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <content type="application/xml">
  <record type="object">
   <id type="integer">1</id>
   <author type="string">foo</author>
   <title type="string">bar</title>
   <content type="string">foobar</content>
   <date type="date-time">2012-03-11T13:37:21Z</date>
  </record>
 </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteCollection()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getCollection());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <content type="application/xml">
  <record type="object">
   <totalResults type="integer">2</totalResults>
   <startIndex type="integer">0</startIndex>
   <itemsPerPage type="integer">8</itemsPerPage>
   <entry type="array">
    <entry type="object">
     <id type="integer">1</id>
     <author type="string">foo</author>
     <title type="string">bar</title>
     <content type="string">foobar</content>
     <date type="date-time">2012-03-11T13:37:21Z</date>
    </entry>
    <entry type="object">
     <id type="integer">2</id>
     <author type="string">foo</author>
     <title type="string">bar</title>
     <content type="string">foobar</content>
     <date type="date-time">2012-03-11T13:37:21Z</date>
    </entry>
   </entry>
  </record>
 </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteComplex()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getComplex());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <content type="application/xml">
  <record type="object">
   <verb type="string">post</verb>
   <actor type="object">
    <id type="string">tag:example.org,2011:martin</id>
    <objectType type="string">person</objectType>
    <displayName type="string">Martin Smith</displayName>
    <url type="string">http://example.org/martin</url>
   </actor>
   <object type="object">
    <id type="string">tag:example.org,2011:abc123/xyz</id>
    <url type="string">http://example.org/blog/2011/02/entry</url>
   </object>
   <target type="object">
    <id type="string">tag:example.org,2011:abc123</id>
    <objectType type="string">blog</objectType>
    <displayName type="string">Martin's Blog</displayName>
    <url type="string">http://example.org/blog/</url>
   </target>
   <published type="date-time">2011-02-10T15:04:55Z</published>
  </record>
 </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getEmpty());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
  <content type="application/xml">
    <record type="object"/>
  </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteArray()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getArray());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <content type="application/xml">
  <collection type="array">
   <entry type="object">
    <id type="integer">1</id>
    <author type="string">foo</author>
    <title type="string">bar</title>
    <content type="string">foobar</content>
    <date type="date-time">2012-03-11T13:37:21Z</date>
   </entry>
   <entry type="object">
    <id type="integer">2</id>
    <author type="string">foo</author>
    <title type="string">bar</title>
    <content type="string">foobar</content>
    <date type="date-time">2012-03-11T13:37:21Z</date>
   </entry>
  </collection>
 </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteArrayScalar()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getArrayScalar());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <content type="application/xml">
  <collection type="array">
   <entry type="string">foo</entry>
   <entry type="string">bar</entry>
  </collection>
 </content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }

    public function testWriteScalar()
    {
        $writer = new Atom();
        $actual = $writer->write($this->getScalar());

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<entry xmlns="http://www.w3.org/2005/Atom">
 <content type="application/xml">foobar</content>
</entry>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $actual, $actual);
    }
}
