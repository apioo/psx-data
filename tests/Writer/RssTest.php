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

namespace PSX\Data\Tests\Writer;

use DateTime;
use PSX\Data\Writer\Rss;
use PSX\Http\MediaType;
use PSX\Model\Rss\Item;
use PSX\Model\Rss\Rss as RssRecord;
use PSX\Record\Record;

/**
 * RssTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RssTest extends \PHPUnit_Framework_TestCase
{
    public function testWriteFeed()
    {
        $writer = new Rss();
        $actual = $writer->write($this->getRssRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
 <channel>
  <title>Liftoff News</title>
  <link>http://liftoff.msfc.nasa.gov/</link>
  <description>Liftoff to Space Exploration.</description>
  <language>en-us</language>
  <managingEditor>editor@example.com</managingEditor>
  <webMaster>webmaster@example.com</webMaster>
  <pubDate>Tue, 10 Jun 2003 04:00:00 +0000</pubDate>
  <lastBuildDate>Tue, 10 Jun 2003 09:41:01 +0000</lastBuildDate>
  <generator>Weblog Editor 2.0</generator>
  <docs>http://blogs.law.harvard.edu/tech/rss</docs>
  <item>
   <title>Star City</title>
   <link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
   <description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href=&quot;http://howe.iki.rssi.ru/GCTC/gctc_e.htm&quot;&gt;Star City&lt;/a&gt;.</description>
   <guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
   <pubDate>Tue, 03 Jun 2003 09:39:21 +0000</pubDate>
  </item>
 </channel>
</rss>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteItem()
    {
        $writer = new Rss();
        $actual = $writer->write($this->getItemRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<item>
 <title>Star City</title>
 <link>http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp</link>
 <description>How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia's &lt;a href=&quot;http://howe.iki.rssi.ru/GCTC/gctc_e.htm&quot;&gt;Star City&lt;/a&gt;.</description>
 <guid>http://liftoff.msfc.nasa.gov/2003/06/03.html#item573</guid>
 <pubDate>Tue, 03 Jun 2003 09:39:21 +0000</pubDate>
</item>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function getRssRecord()
    {
        $rss = new RssRecord();
        $rss->setTitle('Liftoff News');
        $rss->setLink('http://liftoff.msfc.nasa.gov/');
        $rss->setDescription('Liftoff to Space Exploration.');
        $rss->setLanguage('en-us');
        $rss->setPubDate(new DateTime('Tue, 10 Jun 2003 04:00:00 GMT'));
        $rss->setLastBuildDate(new DateTime('Tue, 10 Jun 2003 09:41:01 GMT'));
        $rss->setDocs('http://blogs.law.harvard.edu/tech/rss');
        $rss->setGenerator('Weblog Editor 2.0');
        $rss->setManagingEditor('editor@example.com');
        $rss->setWebMaster('webmaster@example.com');
        $rss->addItem($this->getItemRecord());

        return $rss;
    }

    public function getItemRecord()
    {
        $item = new Item();
        $item->setTitle('Star City');
        $item->setLink('http://liftoff.msfc.nasa.gov/news/2003/news-starcity.asp');
        $item->setDescription('How do Americans get ready to work with Russians aboard the International Space Station? They take a crash course in culture, language and protocol at Russia\'s <a href="http://howe.iki.rssi.ru/GCTC/gctc_e.htm">Star City</a>.');
        $item->setPubDate(new DateTime('Tue, 03 Jun 2003 09:39:21 GMT'));
        $item->setGuid('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573');

        return $item;
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Rss();

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/rss+xml')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
    }

    public function testGetContentType()
    {
        $writer = new Rss();

        $this->assertEquals('application/rss+xml', $writer->getContentType());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidData()
    {
        $writer = new Rss();
        $writer->write(new Record());
    }
}
