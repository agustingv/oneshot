<?php

namespace OneShot\Application\User\Command;

use OneShot\Application\User\Command\CreateUserCommand;
use OneShot\Domain\User\User;
use OneShot\Domain\User\UserRepository;
use OneShot\Domain\ValueObjects\EntityId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserCommandHandler
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateUserCommand $command) : void
    {
        try {
            $command->user->setUserIdentifier(EntityId::generate()->toString());
            $this->repository->create($command->user);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}