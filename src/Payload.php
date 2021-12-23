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

namespace PSX\Data;

/**
 * Payload
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Payload
{
    private mixed $data;
    private string $contentType;
    private ?TransformerInterface $transformer;

    /**
     * Absolute class name of a specific reader or writer
     */
    private ?string $rwType;

    /**
     * Array which contains absolute reader or writer class names to indicate
     * which are supported. By default all available reader or writer
     * implementations are used
     */
    private ?array $rwSupported;

    public function __construct(mixed $data, string $contentType)
    {
        $this->data = $data;
        $this->contentType = $contentType;
        $this->transformer = null;
        $this->rwType = null;
        $this->rwSupported = null;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getContentType(): string
    {
        return $this->contentType ?: 'application/json';
    }

    public function getTransformer(): ?TransformerInterface
    {
        return $this->transformer;
    }

    public function setTransformer(TransformerInterface $transformer): self
    {
        $this->transformer = $transformer;

        return $this;
    }

    public function getRwType(): ?string
    {
        return $this->rwType;
    }

    public function setRwType(string $rwType): self
    {
        $this->rwType = $rwType;

        return $this;
    }

    public function getRwSupported(): ?array
    {
        return $this->rwSupported;
    }

    public function setRwSupported(array $rwSupported): self
    {
        $this->rwSupported = $rwSupported;

        return $this;
    }

    public static function create(mixed $data, string $contentType): self
    {
        return new self($data, $contentType);
    }

    public static function json(mixed $data): self
    {
        return self::create($data, "application/json");
    }

    public static function xml(mixed $data): self
    {
        return self::create($data, "application/xml");
    }

    public static function form(mixed $data): self
    {
        return self::create($data, "application/x-www-form-urlencoded");
    }
}
