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

namespace PSX\Data\Writer;

use DateTime;
use InvalidArgumentException;
use PSX\Data\WriterInterface;
use PSX\Exception;
use PSX\Http\MediaType;
use PSX\Model\Rss as Model;

/**
 * Rss
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Rss implements WriterInterface
{
    protected static $mime = 'application/rss+xml';

    public function write($data)
    {
        if ($data instanceof Model\Rss) {
            $writer = new Rss\Writer($data->getTitle(), $data->getLink(), $data->getDescription());

            $this->buildChannel($data, $writer);

            $items = $data->getItem();
            foreach ($items as $row) {
                $item = $writer->createItem();

                $this->buildItem($row, $item);

                $item->close();
            }

            return $writer->toString();
        } elseif ($data instanceof Model\Item) {
            $writer = new Rss\Item();

            $this->buildItem($data, $writer);

            return $writer->toString();
        } else {
            throw new InvalidArgumentException('Record must be an PSX\Rss or PSX\Rss\Item record');
        }
    }

    public function isContentTypeSupported(MediaType $contentType)
    {
        return $contentType->getName() == self::$mime;
    }

    public function getContentType()
    {
        return self::$mime;
    }

    protected function buildChannel(Model\Rss $rss, Rss\Writer $writer)
    {
        $language = $rss->getLanguage();
        if (!empty($language)) {
            $writer->setLanguage($language);
        }

        $copyright = $rss->getCopyright();
        if (!empty($copyright)) {
            $writer->setCopyright($copyright);
        }

        $managingEditor = $rss->getManagingEditor();
        if (!empty($managingEditor)) {
            $writer->setManagingEditor($managingEditor);
        }

        $webMaster = $rss->getWebMaster();
        if (!empty($webMaster)) {
            $writer->setWebMaster($webMaster);
        }

        $pubDate = $rss->getPubDate();
        if ($pubDate instanceof DateTime) {
            $writer->setPubDate($pubDate);
        }

        $lastBuildDate = $rss->getLastBuildDate();
        if ($lastBuildDate instanceof DateTime) {
            $writer->setLastBuildDate($lastBuildDate);
        }

        $categories = $rss->getCategory();
        if (is_array($categories)) {
            foreach ($categories as $category) {
                $writer->addCategory($category->getText(), $category->getDomain());
            }
        }

        $generator = $rss->getGenerator();
        if (!empty($generator)) {
            $writer->setGenerator($generator);
        }

        $docs = $rss->getDocs();
        if (!empty($docs)) {
            $writer->setDocs($docs);
        }

        $cloud = $rss->getCloud();
        if ($cloud instanceof Model\Cloud) {
            $writer->setCloud($cloud->getDomain(),
                $cloud->getPort(),
                $cloud->getPath(),
                $cloud->getRegisterProcedure(),
                $cloud->getProtocol());
        }

        $ttl = $rss->getTtl();
        if (!empty($ttl)) {
            $writer->setTtl($ttl);
        }

        $rating = $rss->getRating();
        if (!empty($rating)) {
            $writer->setRating($rating);
        }

        $skipHours = $rss->getSkipHours();
        if (!empty($skipHours)) {
            $writer->setSkipHours($skipHours);
        }

        $skipDays = $rss->getSkipDays();
        if (!empty($skipDays)) {
            $writer->setSkipDays($skipDays);
        }
    }

    protected function buildItem(Model\Item $item, Rss\Item $writer)
    {
        $title = $item->getTitle();
        if (!empty($title)) {
            $writer->setTitle($title);
        }

        $link = $item->getLink();
        if (!empty($link)) {
            $writer->setLink($link);
        }

        $description = $item->getDescription();
        if (!empty($description)) {
            $writer->setDescription($description);
        }

        $author = $item->getAuthor();
        if (!empty($author)) {
            $writer->setAuthor($author);
        }

        $categories = $item->getCategory();
        if (is_array($categories)) {
            foreach ($categories as $category) {
                $writer->addCategory($category->getText(), $category->getDomain());
            }
        }

        $comments = $item->getComments();
        if (!empty($comments)) {
            $writer->setComments($comments);
        }

        $enclosure = $item->getEnclosure();
        if ($enclosure instanceof Model\Enclosure) {
            $writer->setEnclosure($enclosure->getUrl(), $enclosure->getLength(), $enclosure->getType());
        }

        $guid = $item->getGuid();
        if (!empty($guid)) {
            $writer->setGuid($guid);
        }

        $pubDate = $item->getPubDate();
        if ($pubDate instanceof DateTime) {
            $writer->setPubDate($pubDate);
        }
    }
}
