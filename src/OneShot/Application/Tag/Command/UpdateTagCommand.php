<?php

namespace OneShot\Application\Tag\Command;

use OneShot\Domain\Tag\Tag;

class UpdateTagCommand
{
    public function __construct(
        public Tag $tag
    ) {}
}