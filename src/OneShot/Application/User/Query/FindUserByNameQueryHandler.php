<?php

namespace OneShot\Application\User\Query;

use OneShot\Application\User\Query\FindUserByNameQuery;
use OneShot\Domain\User\User;
use OneShot\Domain\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FindUserByNameQueryHandler
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(FindUserByNameQuery $query) : array
    {
        try {
            return $this->repository->find_by_name($query->name);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}