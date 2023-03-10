<?php

namespace OneShot\Application\Tag\Query;

use OneShot\Application\Tag\Query\LoadMultipleTagQuery;
use OneShot\Domain\Tag\Tag;
use OneShot\Domain\Tag\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LoadMultipleTagQueryHandler
{
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(LoadMultipleTagQuery $query) : array
    {
        try {
           return $this->repository->find_by_ids(
                ids: $query->ids
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
