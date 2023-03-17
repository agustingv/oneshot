<?php

namespace OneShot\Application\Post\Query;

use OneShot\Domain\Post\Post;
use App\Middleware\CachableQueryResult;

class ViewPostByDateQuery implements CachableQueryResult
{
    public function __construct(
        public int $page,
        public int $items_page
    ) {}

    public function getCacheContexts() : array
    {
        return $cacheContexts = [
            'id' => sprintf('view_post_date_%s', $this->page.$this->items_page),
            'tags' => ['date_lists']
        ];
    }
}
