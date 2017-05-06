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

namespace PSX\Data\Tests\Processor;

use PSX\Data\Payload;
use PSX\Data\Tests\ProcessorTestCase;
use PSX\Model\Rss\Rss;

/**
 * RssTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RssTest extends ProcessorTestCase
{
    public function testReadRss()
    {
        $body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
		<title>Liftoff News</title>
		<link>http://liftoff.msfc.nasa.gov/</link>
		<description>Liftoff to Space Exploration.</description>
		<language>en-us</language>
		<pubDate>2003-06-10T04:00:00Z</pubDate>
		<lastBuildDate>2003-06-10T09:41:01Z</lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<generator>Weblog Editor 2.0</generator>
		<managingEditor>editor@example.com</managingEditor>
		<webMaster>webmaster@example.com</webMaster>
		<item>
			<title>Star City</title>
			<link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
			<description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russias &lt;a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm"&gt;Star City&lt;/a&gt;.</description>
			<pubDate>2003-06-03T09:39:21Z</pubDate>
			<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
		</item>
	</channel>
</rss>
XML;

        $rss = $this->processor->read(Rss::class, Payload::create($body, 'application/rss+xml'));

        $this->assertInstanceOf('PSX\Model\Rss\Rss', $rss);
        $this->assertEquals('Liftoff News', $rss->getTitle());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/', $rss->getLink());
        $this->assertEquals('Liftoff to Space Exploration.', $rss->getDescription());
        $this->assertEquals('en-us', $rss->getLanguage());
        $this->assertEquals('2003-06-10', $rss->getPubDate()->format('Y-m-d'));
        $this->assertEquals('2003-06-10', $rss->getLastBuildDate()->format('Y-m-d'));
        $this->assertEquals('http://blogs.law.harvard.edu/tech/rss', $rss->getDocs());
        $this->assertEquals('Weblog Editor 2.0', $rss->getGenerator());
        $this->assertEquals('editor@example.com', $rss->getManagingEditor());
        $this->assertEquals('webmaster@example.com', $rss->getWebMaster());

        $item = $rss->getItem()[0];

        $this->assertEquals('Star City', $item->getTitle());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp', $item->getLink());
        $this->assertEquals('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russias <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', $item->getDescription());
        $this->assertEquals('2003-06-03', $item->getPubDate()->format('Y-m-d'));
        $this->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->getGuid());
    }

    public function testItem()
    {
        $body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<item>
	<title>Star City</title>
	<link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
	<description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm"&gt;Star City&lt;/a&gt;.</description>
	<pubDate>2003-06-03T09:39:21Z</pubDate>
	<guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
</item>
XML;

        $rss  = $this->processor->read(Rss::class, Payload::create($body, 'application/rss+xml'));
        $item = $rss->getItem()[0];

        $this->assertInstanceOf('PSX\Model\Rss\Item', $item);
        $this->assertEquals('Star City', $item->getTitle());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp', $item->getLink());
        $this->assertEquals('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.', $item->getDescription());
        $this->assertEquals(new \DateTime('Tue, 03 Jun 2003 09:39:21 GMT'), $item->getPubDate());
        $this->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->getGuid());
    }
}
