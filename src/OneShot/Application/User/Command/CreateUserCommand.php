<?php

namespace OneShot\Application\User\Command;

use OneShot\Domain\User\User;

class CreateUserCommand
{
    public function __construct(
        public User $user
    ) {}
}