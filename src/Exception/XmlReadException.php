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

namespace PSX\Data\Exception;

use LibXMLError;
use Throwable;

/**
 * XmlReadException
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class XmlReadException extends ReadException
{
    /**
     * @var array<LibXMLError>
     */
    private array $errors;

    /**
     * @param array<LibXMLError> $errors
     */
    public function __construct(array $errors, ?Throwable $previous = null)
    {
        parent::__construct($this->buildMessage($errors), previous: $previous);

        $this->errors = $errors;
    }

    /**
     * @return array<LibXMLError>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array<LibXMLError> $errors
     */
    private function buildMessage(array $errors): string
    {
        $result = [];
        foreach ($errors as $error) {
            $result[] = trim($error->message) . ' on line ' . $error->line . ' at column ' . $error->column;
        }

        return 'Could not parse xml payload: ' . implode(', ', $result);
    }
}
