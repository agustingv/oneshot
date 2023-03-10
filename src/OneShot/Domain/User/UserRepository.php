<?php

namespace OneShot\Domain\User;

use OneShot\Domain\User\User;
use OneShot\Domain\ValueObjects\EntityId;

interface UserRepository
{
    public function create(User $user) : void;
    public function update(string $id, array $properties, User $user) : void;
    public function delete(User $user) : void;
    public function load(string $id) : User;
    public function find_by_id(EntityId $id) : User;
    public function find_by_name(string $name) : array;
    public function verifyToken(string $idToken) : Object;
}