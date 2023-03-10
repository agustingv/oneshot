<?php

namespace OneShot\Application\Tag\Query;

class LoadMultipleTagQuery
{
    public function __construct(
        public array $ids
    ) {}
}
