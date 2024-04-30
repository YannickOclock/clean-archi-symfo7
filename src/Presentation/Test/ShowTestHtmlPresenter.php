<?php

declare(strict_types=1);

namespace App\Presentation\Test;

use App\Domain\TestPost\UseCase\ShowPosts\ShowPostsPresenterInterface;
use App\Domain\TestPost\UseCase\ShowPosts\ShowPostsResponse;

final class ShowTestHtmlPresenter implements ShowPostsPresenterInterface
{
    private ShowTestHtmlViewModel $viewModel;

    public function present(ShowPostsResponse $response): void
    {
        $this->viewModel = new ShowTestHtmlViewModel();
        $this->viewModel->posts = $response->getPosts();
    }

    public function viewModel(): ShowTestHtmlViewModel
    {
        return $this->viewModel;
    }
}
