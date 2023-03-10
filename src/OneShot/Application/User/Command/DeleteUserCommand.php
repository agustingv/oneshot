<?php

namespace OneShot\Application\User\Command;

use OneShot\Domain\User\User;

class DeleteUserCommand
{
    public function __construct(
        public User $user
    ) {}
}