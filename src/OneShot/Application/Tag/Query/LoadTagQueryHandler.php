<?php

namespace OneShot\Application\Tag\Query;

use OneShot\Application\Tag\Query\LoadTagQuery;
use OneShot\Domain\Tag\Tag;
use OneShot\Domain\Tag\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class LoadTagQueryHandler
{
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(LoadTagQuery $query) : Tag
    {
        try {
           return $this->repository->find_by_id(
                id: $query->id
            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
