<?php

namespace OneShot\Application\User\Query;

use OneShot\Application\User\Query\LoadUserQuery;
use OneShot\Domain\User\User;
use OneShot\Domain\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LoadUserQueryHandler
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(LoadUserQuery $query) : User
    {
        try {
            return $this->repository->load($query->id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}