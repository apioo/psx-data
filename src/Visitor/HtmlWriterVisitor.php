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

namespace PSX\Data\Visitor;

use PSX\Data\VisitorAbstract;
use PSX\DateTime\DateTime;
use PSX\DateTime\LocalDateTime;
use PSX\DateTime\Period;

/**
 * HtmlWriterVisitor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class HtmlWriterVisitor extends VisitorAbstract
{
    private string $output;

    public function __construct()
    {
        $this->output = '';
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function visitObjectStart()
    {
        $this->write('<dl>');
    }

    public function visitObjectEnd()
    {
        $this->write('</dl>');
    }

    public function visitObjectValueStart(string $key, mixed $value)
    {
        $this->write('<dt>' . htmlspecialchars($key) . '</dt><dd>');
    }

    public function visitObjectValueEnd()
    {
        $this->write('</dd>');
    }

    public function visitArrayStart()
    {
        $this->write('<ul>');
    }

    public function visitArrayEnd()
    {
        $this->write('</ul>');
    }

    public function visitArrayValueStart(mixed $value)
    {
        $this->write('<li>');
    }

    public function visitArrayValueEnd()
    {
        $this->write('</li>');
    }

    public function visitValue(mixed $value)
    {
        $this->write($this->getValue($value));
    }

    protected function write(string $message)
    {
        $this->output.= $message;
    }

    protected function getValue(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return LocalDateTime::from($value)->toString();
        } elseif ($value instanceof \DateInterval) {
            return Period::from($value)->toString();
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } else {
            return htmlspecialchars((string) $value);
        }
    }
}
