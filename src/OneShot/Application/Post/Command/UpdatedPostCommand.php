<?php

namespace OneShot\Application\Post\Command;

use OneShot\Domain\Post\Post;

class UpdatedPostCommand
{
    public function __construct(
        public Post $post
    ) {}
}
