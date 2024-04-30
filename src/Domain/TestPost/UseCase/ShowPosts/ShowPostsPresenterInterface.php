<?php

namespace App\Domain\TestPost\UseCase\ShowPosts;

interface ShowPostsPresenterInterface
{
    public function present(ShowPostsResponse $response): void;
}