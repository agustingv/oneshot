<?php

namespace OneShot\Domain\Tag;

use OneShot\Domain\Tag\Tag;

interface TagRepository
{
    public function find_by_id(string $id) : ?Tag;
    public function find_by_name(string $name) : array;
    public function find_by_ids(array $ids) : array;
    public function create(Tag $tag) : void;
    public function update(Tag $tag) : void;
    public function delete(Tag $tag) : void;
}
