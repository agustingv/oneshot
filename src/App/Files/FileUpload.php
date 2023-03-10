<?php

namespace App\Files;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use OneShot\Domain\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use OneShot\Domain\ValueObjects\EntityId;

class FileUpload 
{
    public static function uploadFile($data, $directory, $uploadDir) : array | null
    {
        $imageFile = $data;
        $slugger = new AsciiSlugger();

        if ($imageFile) 
        {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $directory,
                    $newFilename
                );
            } catch (FileException $e) {
                $rrors[] = $e->getMessage();
            }
            $filePath = $uploadDir . '/' . $newFilename;

            return ['path' => $filePath, 'name' => $newFilename];

        }

        return null;
    }

    public static function prepareNewFiles(UploadedFile $file, string $path) : File
    {
       
        $name = EntityId::generate()->toString();
        $originalName = $file->getClientOriginalName();
        $mime = $file->getClientMimeType();
        $ext = $file->guessExtension();
        $info = getimagesize($file);

        $newFile = new File();
        $newFile->setId(EntityId::generate()->toString());
        $newFile->setPath($path.$name.'.'.$ext);
        $newFile->setUri($path.$name.'.'.$ext);
        $newFile->setMime($mime);
        $newFile->setWidth($info[0]);
        $newFile->setHeight($info[1]); 

        return $newFile;
    }
}