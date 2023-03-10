<?php

namespace OneShot\Application\User\Command;

use OneShot\Domain\User\User;

class UpdateUserCommand
{
    public function __construct(
        public string $id,
        public array $properties,
        public User $user
    ) {}
}