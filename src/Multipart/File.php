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

namespace PSX\Data\Multipart;

use PSX\Data\Exception\UploadException;

/**
 * File
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class File implements \JsonSerializable
{
    private ?string $name;
    private ?string $type;
    private ?int $size;
    private ?string $tmpName;
    private ?int $error;

    public function __construct(?string $name, ?string $type, ?int $size, ?string $tmpName, ?int $error)
    {
        $this->name = $name;
        $this->type = $type;
        $this->size = $size;
        $this->tmpName = $tmpName;
        $this->error = $error;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getTmpName(): ?string
    {
        return $this->tmpName;
    }

    public function getError(): ?int
    {
        return $this->error;
    }

    /**
     * Moves a temporary uploaded file to a destination
     *
     * @throws UploadException
     */
    public function moveTo(string $path): bool
    {
        if (!$this->isValidUpload()) {
            throw new UploadException('Invalid file upload');
        }

        return $this->moveUploadedFile($path);
    }

    /**
     * Checks whether the file upload is valid
     *
     * @throws UploadException
     */
    private function isValidUpload(): bool
    {
        $error = $this->error ?? null;

        switch ($error) {
            case UPLOAD_ERR_OK:
                return $this->isUploadedFile();

            case UPLOAD_ERR_INI_SIZE:
                throw new UploadException('The uploaded file exceeds the upload_max_filesize directive in php.ini', UPLOAD_ERR_INI_SIZE);

            case UPLOAD_ERR_FORM_SIZE:
                throw new UploadException('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', UPLOAD_ERR_FORM_SIZE);

            case UPLOAD_ERR_PARTIAL:
                throw new UploadException('The uploaded file was only partially uploaded', UPLOAD_ERR_PARTIAL);

            case UPLOAD_ERR_NO_FILE:
                throw new UploadException('No file was uploaded', UPLOAD_ERR_NO_FILE);

            case UPLOAD_ERR_NO_TMP_DIR:
                throw new UploadException('Missing a temporary folder', UPLOAD_ERR_NO_TMP_DIR);

            case UPLOAD_ERR_CANT_WRITE:
                throw new UploadException('Failed to write file to disk', UPLOAD_ERR_CANT_WRITE);

            case UPLOAD_ERR_EXTENSION:
                throw new UploadException('A PHP extension stopped the file upload', UPLOAD_ERR_EXTENSION);

            default:
                throw new UploadException('Invalid error code');
        }
    }

    private function isUploadedFile(): bool
    {
        return is_uploaded_file($this->tmpName);
    }

    private function moveUploadedFile(string $path): bool
    {
        return move_uploaded_file($this->tmpName, $path);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'size' => $this->size,
            'tmpName' => $this->tmpName,
            'error' => $this->error,
        ];
    }

    public static function fromArray(array|\ArrayAccess $file): self
    {
        return new self(
            $file['name'] ?? null,
            $file['type'] ?? null,
            $file['size'] ?? null,
            $file['tmp_name'] ?? null,
            $file['error'] ?? null
        );
    }
}
