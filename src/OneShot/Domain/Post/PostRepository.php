<?php

namespace OneShot\Domain\Post;

use OneShot\Domain\Post\Post;

interface PostRepository
{
    public function find_by_id(string $id) : ?Post;
    public function find_by_tag(array $tag) : array;
    public function find_pager(int $page, int $items_page) : array;
    public function create(Post $post) : void;
    public function update(Post $post) : void;
    public function delete(Post $post) : void;

}
