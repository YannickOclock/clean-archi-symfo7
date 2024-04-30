<?php

namespace App\Domain\TestPost\UseCase\ShowPosts;

use App\Domain\TestPost\Repository\PostsRepositoryInterface;

readonly class ShowPostsUseCase implements ShowPostsUseCaseInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepositoryInterface
    ) {}

    public function execute(ShowPostsRequest $request, ShowPostsResponse $response, ShowPostsPresenterInterface $presenter): void
    {
        // Tests avec des posts
        $posts = $this->postsRepositoryInterface->all();
        $response->setPosts($posts);
        $presenter->present($response);
    }
}