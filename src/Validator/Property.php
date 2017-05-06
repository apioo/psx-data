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

namespace PSX\Data\Validator;

/**
 * Property
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @deprecated 
 */
class Property extends \PSX\Schema\Validation\Field
{
    /**
     * The name should be a JSON pointer which locates the target property i.e.
     * /author/name
     *
     * @param string $name
     * @param string $type
     * @param array $filters
     * @param boolean $required
     */
    public function __construct($name, $type, array $filters = array(), $required = true)
    {
        parent::__construct($name, $filters);
    }

    public function getType()
    {
        return null;
    }

    public function isRequired()
    {
        return true;
    }
}
