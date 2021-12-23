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

namespace PSX\Data\Writer;

use PSX\Http\MediaType;

/**
 * Jsonp
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Jsonp extends Json
{
    protected const MIME = 'application/javascript';

    private ?string $callbackName = null;

    public function write(mixed $data): string
    {
        $callbackName = $this->getCallbackName();

        if (!empty($callbackName)) {
            return $callbackName . '(' . parent::write($data) . ')';
        } else {
            return parent::write($data);
        }
    }

    public function isContentTypeSupported(MediaType $contentType): bool
    {
        return $contentType->getName() == self::MIME;
    }

    public function getContentType(): string
    {
        return self::MIME;
    }

    public function getCallbackName(): ?string
    {
        return $this->callbackName;
    }

    public function setCallbackName(string $callbackName): void
    {
        if (preg_match('/^([A-Za-z0-9._]{3,64})$/', $callbackName)) {
            $this->callbackName = $callbackName;
        }
    }
}
