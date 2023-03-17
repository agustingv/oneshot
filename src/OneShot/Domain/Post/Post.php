<?php

namespace OneShot\Domain\Post;

use OneShot\Domain\Shared\AbstractDomainModel;
use OneShot\Domain\User\User;

class Post extends AbstractDomainModel
{
    public string $body;
    public int $createdAt;
    public string $id;
    public int $expiredAt = 0;
    public int $points = 100;
    public ?User $user;
    protected ?array $tags = null;

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

    public function setBody(string $body) : void
    {
        $this->body = $body;
    }

    public function getBody() : string
    {
        return $this->body;
    }

    public function setCreatedAt (int $timestamp = null) : void
    {
        if ($timestamp === null)
        {
            $this->createdAt = time();
        } else {
            $this->createdAt = $timestamp;
        }
    }

    public function getCreatedAt() : int
    {
        return $this->createdAt;
    }

    public function setExpiredAt(int $timestamp = null) : void
    {
        if ($timestamp === null)
        {
            if ($this->points === 0)
            {
                $time = time();
            } else {
                $time = time() + (60 * 60 * 24 * 30);
            }
            $this->expiredAt = $time;
        } else {
            $this->expiredAt = $timestamp;
        }
    }

    public function getExpiredAt() : int
    {
        return $this->expiredAt;
    }

    public function setPoints(int $points) : void
    {
        $this->points = $points;
    }

    public function getPoints() : int
    {
        return $this->points;
    }

    public function setUser(User $user) : void
    {
        $this->user = $user;
    }

    public function getUser() : User
    {
        return $this->user;
    }

    public function setTags(array|null $tags) : void
    {
        $this->tags = $tags;
    }

    public function getTags() : array | null
    {
        return $this->tags;
    }

    public function fromArray(array $data) : self
    {
        $data = $this->deserialize($data);
        return $this->serializer->denormalize($data, Post::class);
    }

}