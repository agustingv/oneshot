<?php

namespace OneShot\Domain\File;

use OneShot\Domain\Shared\AbstractDomainModel;

class File extends AbstractDomainModel
{

    private string $id;
    private string $path;
    private string $uri;
    private string $mime;
    private int $width = 0;
    private int $height = 0;
    private string $title = "";
    private string $alt = "";
    protected string $type = self::class;

    public function __construct()
    {
        parent::__construct();
    }

    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function setPath(string $path) : void
    {
        $this->path = $path;
    }
    
    public function getPath() : string
    {
        return $this->path;
    }

    public function setUri(string $uri) : void
    {
        $this->uri = $uri;
    }

    public function getUri() : string
    {
        return $this->uri;
    }

    public function setMime(string $mime) : void
    {
        $this->mime = $mime;
    }

    public function getMime() : string
    {
        return $this->mime;
    }

    public function setWidth(int $width) : void
    {
        $this->width = $width;
    }

    public function getWidth() : int
    {
        return $this->width;
    }

    public function setHeight(int $height) : void
    {
        $this->height = $height;
    }

    public function getHeight() : int
    {
        return $this->height;
    }

    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function setAlt(string $alt) : void
    {
        $this->alt = $alt;
    }

    public function getAlt() : string
    {
        return $this->alt;
    }
    
    public function getType() : string
    {
        if (!isset($this->type))
        {
            $this->type = self::class;
        }

        return $this->type;
    }

    public function fromArray(array $data) : self
    {
        $data = $this->deserialize($data);
        return $this->serializer->denormalize($data, File::class);
    }    
}