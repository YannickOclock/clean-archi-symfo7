<?php

declare(strict_types=1);

namespace App\Domain\TestPost\UseCase\ShowPosts;


class ShowPostsResponse
{
    private array $posts;

    public function getPosts(): array
    {
        return $this->posts;
    }

    public function setPosts(array $posts): void
    {
        $this->posts = $posts;
    }
}
