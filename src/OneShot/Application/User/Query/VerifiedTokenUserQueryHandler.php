<?php

namespace OneShot\Application\User\Query;

use OneShot\Application\User\Query\VerifiedTokenUserQuery;
use OneShot\Domain\User\User;
use OneShot\Domain\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VerifiedTokenUserQueryHandler
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(VerifiedTokenUserQuery $command) : Object
    {
        try {
           return $this->repository->verifyToken(
                idToken: $command->idToken,
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}