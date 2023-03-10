<?php

namespace OneShot\Application\Post\Query;

use OneShot\Domain\Post\Post;

class ViewPostByTagQuery
{
    public function __construct(
        public array $tag
    ) {}
}
