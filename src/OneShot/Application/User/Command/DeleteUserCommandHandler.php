<?php

namespace OneShot\Application\User\Command;

use OneShot\Application\User\Command\DeleteUserCommand;
use OneShot\Domain\User\User;
use OneShot\Domain\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteUserCommandHandler
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteUserCommand $command) : void
    {
        try {
            $this->repository->delete($command->user);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}