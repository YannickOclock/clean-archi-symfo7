<?php

namespace App\Domain\TestPost\UseCase\ShowPosts;

interface ShowPostsUseCaseInterface
{
    public function execute(ShowPostsRequest $request, ShowPostsResponse $response, ShowPostsPresenterInterface $presenter): void;
}