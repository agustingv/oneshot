<?php

namespace OneShot\Domain\File;

use OneShot\Domain\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileRepository
{
    public function find_by_id(string $id) : ?File;
    public function create(File $file, UploadedFile $upFile) : string;
    public function update(File $file) : void;
    public function delete(File $file) : void;
}
