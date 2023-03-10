<?php

namespace OneShot\Application\File\Command;

use OneShot\Domain\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateFileCommand
{
    public function __construct(
        public File $file,
        public UploadedFile $upFile
    ) {}
}