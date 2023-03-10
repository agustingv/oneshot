<?php

namespace OneShot\Application\Tag\Query;

use OneShot\Application\Tag\Query\AutocompleteTagQuery;
use OneShot\Domain\Tag\Tag;
use OneShot\Domain\Tag\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AutocompleteTagQueryHandler
{
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(AutocompleteTagQuery $query) : array
    {
        try {
           return $this->repository->find_by_name(
                name: $query->name,

            );
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
