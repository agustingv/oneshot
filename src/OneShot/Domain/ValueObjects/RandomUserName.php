<?php

namespace OneShot\Domain\ValueObjects;

class RandomUserName
{

    public function __construct(private string $name)
    {}

    public static function generate() : self
    {
        return new self(\Faker\Factory::create()->unique()->word());
    }

    public function toString() : string
    {
        return $this->name;
    }
}