<?php

namespace OneShot\Domain\Tag;

use OneShot\Domain\Shared\AbstractDomainModel;
use OneShot\Domain\ValueObjects\EntityId;

class Tag extends AbstractDomainModel
{
    private string $id;
    private string $name;
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

    public function setName(string $name) : void
    {
        $this->name = $name;
    } 

    public function getName() : string
    {
        return $this->name;
    }

    public function getType() : string
    {
        if (!isset($this->type))
        {
            $this->type = self::class;
        }

        return $this->type;
    }  

    public function fromArray(array $data) : Tag
    {
        $data = $this->deserialize($data);
        return $this->serializer->denormalize($data, Tag::class);
    }

}