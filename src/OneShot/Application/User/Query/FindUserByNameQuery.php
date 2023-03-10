<?php

namespace OneShot\Application\User\Query;

class FindUserByNameQuery
{
    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }
}