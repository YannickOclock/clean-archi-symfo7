<?php

namespace App\Domain\TestPost\Repository;

use App\Domain\TestPost\Entity\Post;

interface PostsRepositoryInterface
{
    /**
     * @return iterable|Post[]
     */
    public function all(): iterable;

    public function add(Post $post): void;

    public function remove(Post $post): void;

    public function ofId(string $postId): ?Post; 
}