<?php

namespace OneShot\Application\Tag\Command;

use OneShot\Domain\Tag\Tag;

class CreateTagCommand
{
    public function __construct(
        public Tag $tag
    ) {}
}