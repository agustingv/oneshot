<?php

namespace OneShot\Application\User\Command;

use OneShot\Application\User\Command\UpdateUserCommand;
use OneShot\Domain\User\User;
use OneShot\Domain\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateUserCommandHandler
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateUserCommand $command) : void
    {
        try {
            $this->repository->update(
                id: $command->id,
                properties: $command->properties,
                user: $command->user
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}