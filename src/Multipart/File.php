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

namespace PSX\Data\Multipart;

/**
 * File
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @Title("file")
 * @Description("File upload provided through a multipart/form-data post")
 * @AdditionalProperties(false)
 */
class File
{
    /**
     * @Key("name")
     * @Type("string")
     */
    public $name;

    /**
     * @Key("type")
     * @Type("string")
     */
    public $type;

    /**
     * @Key("size")
     * @Type("integer")
     */
    public $size;

    /**
     * @Key("tmp_name")
     * @Type("string")
     */
    public $tmpName;

    /**
     * @Key("error")
     * @Type("integer")
     */
    public $error;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param integer $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getTmpName()
    {
        return $this->tmpName;
    }

    /**
     * @param string $tmpName
     */
    public function setTmpName($tmpName)
    {
        $this->tmpName = $tmpName;
    }

    /**
     * @return integer
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param integer $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * Moves a temporary uploaded file to a destination
     * 
     * @param string $path
     * @return boolean
     */
    public function moveTo($path)
    {
        if (!$this->isValidUpload()) {
            throw new \RuntimeException('Invalid file upload');
        }

        return $this->moveUploadedFile($path);
    }

    /**
     * Checks whether the file upload is valid
     * 
     * @return boolean
     */
    protected function isValidUpload()
    {
        $error = $this->error ?: UPLOAD_ERR_NO_FILE;

        switch ($error) {
            case UPLOAD_ERR_OK:
                return $this->isUploadedFile();
                break;

            case UPLOAD_ERR_INI_SIZE:
                throw new \RuntimeException('The uploaded file exceeds the upload_max_filesize directive in php.ini');
                break;

            case UPLOAD_ERR_FORM_SIZE:
                throw new \RuntimeException('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
                break;

            case UPLOAD_ERR_PARTIAL:
                throw new \RuntimeException('The uploaded file was only partially uploaded');
                break;

            case UPLOAD_ERR_NO_FILE:
                throw new \RuntimeException('No file was uploaded');
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                throw new \RuntimeException('Missing a temporary folder');
                break;

            case UPLOAD_ERR_CANT_WRITE:
                throw new \RuntimeException('Failed to write file to disk');
                break;

            case UPLOAD_ERR_EXTENSION:
                throw new \RuntimeException('A PHP extension stopped the file upload');
                break;

            default:
                throw new \RuntimeException('Invalid error code');
                break;
        }
    }

    /**
     * Checks whether the file was uploaded
     * 
     * @return boolean
     */
    protected function isUploadedFile()
    {
        return is_uploaded_file($this->tmpName);
    }

    /**
     * Moves the uploaded file
     * 
     * @param string $path
     * @return boolean
     */
    protected function moveUploadedFile($path)
    {
        return move_uploaded_file($this->tmpName, $path);
    }

    /**
     * @param array|\ArrayAccess $file
     * @return \PSX\Data\Multipart\File
     */
    public static function fromArray($file)
    {
        $self = new self();
        $self->setName($file['name'] ?? null);
        $self->setType($file['type'] ?? null);
        $self->setSize($file['size'] ?? null);
        $self->setTmpName($file['tmp_name'] ?? null);
        $self->setError($file['error'] ?? null);

        return $self;
    }
}
