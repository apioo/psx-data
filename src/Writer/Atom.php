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
use PSX\Exception;
use PSX\Http\MediaType;
use PSX\Model\Atom as Model;

/**
 * Atom
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Atom extends Xml
{
    protected static $mime = 'application/atom+xml';

    public function write($data)
    {
        if ($data instanceof Model\Atom) {
            $writer = new Atom\Writer($data->getTitle(), $data->getId(), $data->getUpdated());

            $this->buildFeed($data, $writer);

            $entries = $data->getEntry();
            foreach ($entries as $row) {
                $entry = $writer->createEntry();

                $this->buildEntry($row, $entry);

                $entry->close();
            }

            return $writer->toString();
        } elseif ($data instanceof Model\Entry) {
            $writer = new Atom\Entry();

            $this->buildEntry($data, $writer);

            return $writer->toString();
        } else {
            $writer = new Atom\Entry();
            $writer->setContent($data, 'application/xml');

            return $writer->toString();
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

    protected function buildFeed(Model\Atom $atom, Atom\Writer $writer)
    {
        $subTitle = $atom->getSubTitle();
        if (!empty($subTitle)) {
            $writer->setSubTitle($subTitle->getType(), $subTitle->getContent());
        }

        $links = $atom->getLink();
        if (is_array($links)) {
            foreach ($links as $link) {
                $writer->addLink($link->getHref(), $link->getRel(), $link->getType(), $link->getHrefLang(), $link->getTitle(), $link->getLength());
            }
        }

        $rights = $atom->getRights();
        if (!empty($rights)) {
            $writer->setRights($rights);
        }

        $generator = $atom->getGenerator();
        if (!empty($generator)) {
            $writer->setGenerator($generator->getText(), $generator->getUri(), $generator->getVersion());
        }

        $authors = $atom->getAuthor();
        if (is_array($authors)) {
            foreach ($authors as $author) {
                $writer->addAuthor($author->getName(), $author->getUri(), $author->getEmail());
            }
        }

        $categories = $atom->getCategory();
        if (is_array($categories)) {
            foreach ($categories as $category) {
                $writer->addCategory($category->getTerm(), $category->getScheme(), $category->getLabel());
            }
        }

        $contributors = $atom->getContributor();
        if (is_array($contributors)) {
            foreach ($contributors as $contributor) {
                $writer->addContributor($contributor->getName(), $contributor->getUri(), $contributor->getEmail());
            }
        }

        $icon = $atom->getIcon();
        if (!empty($icon)) {
            $writer->setIcon($icon);
        }

        $logo = $atom->getLogo();
        if (!empty($logo)) {
            $writer->setLogo($logo);
        }
    }

    protected function buildEntry(Model\Entry $entry, Atom\Entry $writer)
    {
        $id = $entry->getId();
        if (!empty($id)) {
            $writer->setId($id);
        }

        $title = $entry->getTitle();
        if (!empty($title)) {
            $writer->setTitle($title);
        }

        $updated = $entry->getUpdated();
        if ($updated instanceof DateTime) {
            $writer->setUpdated($updated);
        }

        $published = $entry->getPublished();
        if ($published instanceof DateTime) {
            $writer->setPublished($published);
        }

        $links = $entry->getLink();
        if (is_array($links)) {
            foreach ($links as $link) {
                $writer->addLink($link->getHref(), $link->getRel(), $link->getType(), $link->getHrefLang(), $link->getTitle(), $link->getLength());
            }
        }

        $rights = $entry->getRights();
        if (!empty($rights)) {
            $writer->setRights($rights);
        }

        $authors = $entry->getAuthor();
        if (is_array($authors)) {
            foreach ($authors as $author) {
                $writer->addAuthor($author->getName(), $author->getUri(), $author->getEmail());
            }
        }

        $categories = $entry->getCategory();
        if (is_array($categories)) {
            foreach ($categories as $category) {
                $writer->addCategory($category->getTerm(), $category->getScheme(), $category->getLabel());
            }
        }

        $contributors = $entry->getContributor();
        if (is_array($contributors)) {
            foreach ($contributors as $contributor) {
                $writer->addContributor($contributor->getName(), $contributor->getUri(), $contributor->getEmail());
            }
        }

        $content = $entry->getContent();
        if ($content instanceof Model\Text) {
            $writer->setContent($content->getContent(), $content->getType());
        }

        $summary = $entry->getSummary();
        if ($summary instanceof Model\Text) {
            $writer->setSummary($summary->getContent(), $summary->getType());
        }

        $source = $entry->getSource();
        if ($source instanceof Model\Atom) {
            $sourceWriter = new Atom\Writer($source->getTitle(), $source->getId(), $source->getUpdated(), $writer->getWriter(), 'source', false);

            $this->buildFeed($source, $sourceWriter);

            $sourceWriter->close();
        }
    }
}
