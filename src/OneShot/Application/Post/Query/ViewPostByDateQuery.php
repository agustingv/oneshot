<?php

namespace OneShot\Application\Post\Query;

use OneShot\Domain\Post\Post;

class ViewPostByDateQuery
{
    public function __construct(
        public int $page,
        public int $items_page
    ) {}
}
