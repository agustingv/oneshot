<?php

namespace OneShot\Application\Post\Query;

use OneShot\Domain\Post\PostRepository;
use OneShot\Application\Post\Query\ViewPostByDateQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ViewPostByDateQueryHandler
{
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ViewPostByDateQuery $query) : array
    {
        try {
            return $this->repository->find_pager($query->page, $query->items_page);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}