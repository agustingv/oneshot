<?php

namespace OneShot\Application\User\Query;

class LoadUserQuery
{
    public function __construct(
        string $id
    ) {
        $this->id = $id;
    }
}