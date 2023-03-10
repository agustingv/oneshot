<?php

namespace OneShot\Application\User\Query;

use OneShot\Application\User\Query\SingInUserQuery;
use OneShot\Domain\User\User;
use OneShot\Domain\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SingInUserQueryHandler
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(SingInUserQuery $query) : string
    {
        try {
           return $this->repository->singIn(
                mail: $query->mail,
                password: $query->password
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}