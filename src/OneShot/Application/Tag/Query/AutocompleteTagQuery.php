<?php

namespace OneShot\Application\Tag\Query;

class AutocompleteTagQuery
{
    public function __construct(
        string $name,
    ) {
        $this->name = $name;
    }
}
