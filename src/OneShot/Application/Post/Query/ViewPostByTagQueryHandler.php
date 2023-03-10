<?php

namespace OneShot\Application\Post\Query;

use OneShot\Domain\Post\PostRepository;
use OneShot\Application\Post\Query\ViewPostByTagQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ViewPostByTagQueryHandler
{
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ViewPostByTagQuery $query) : array
    {
        try {
            return $this->repository->find_by_tag($query->tag);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}