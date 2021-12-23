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

namespace PSX\Data\Visitor;

use PSX\Data\VisitorAbstract;
use PSX\DateTime\DateTime;

/**
 * TextWriterVisitor
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TextWriterVisitor extends VisitorAbstract
{
    public const IN_OBJECT = 0x1;
    public const IN_ARRAY  = 0x2;

    private string $output;
    private int $nested = -1;
    private array $types = array();

    public function __construct()
    {
        $this->output = '';
    }

    public function getOutput(): string
    {
        return $this->output;
    }

    public function visitObjectStart(string $name)
    {
        $this->writeLn('Object(' . $name . '){', $this->nested != -1 && $this->types[$this->nested] == self::IN_ARRAY);

        $this->nested++;
        $this->types[] = self::IN_OBJECT;
    }

    public function visitObjectEnd()
    {
        $this->nested--;
        array_pop($this->types);

        $this->writeLn('}');
    }

    public function visitObjectValueStart(string $key, mixed $value)
    {
        $this->write($key . ' = ');
    }

    public function visitArrayStart()
    {
        $this->writeLn('Array[', $this->nested != -1 && $this->types[$this->nested] == self::IN_ARRAY);

        $this->nested++;
        $this->types[] = self::IN_ARRAY;
    }

    public function visitArrayEnd()
    {
        $this->nested--;
        array_pop($this->types);

        $this->writeLn(']');
    }

    public function visitValue(mixed $value)
    {
        $this->writeLn($this->getValue($value), $this->types[$this->nested] == self::IN_ARRAY);
    }

    protected function writeLn(string $message, bool $padding = true)
    {
        $this->write($message . PHP_EOL, $padding);
    }

    protected function write(string $message, bool $padding = true)
    {
        if ($padding) {
            $this->output.= str_repeat(' ', ($this->nested + 1) * 4);
        }

        $this->output.= $message;
    }

    protected function getValue(mixed $value): string
    {
        if ($value instanceof \DateTime) {
            return DateTime::getFormat($value);
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } else {
            $value = (string) $value;
            $value = str_replace(array("\r\n", "\n", "\r"), ' ', $value);
            if (strlen($value) > 32) {
                $value = substr($value, 0, 32) . ' (...)';
            }

            return $value;
        }
    }
}
