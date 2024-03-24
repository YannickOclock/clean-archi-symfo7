<?php

declare(strict_types=1);

namespace App\Presentation\Home;

use App\Domain\Home\UseCase\ShowHome\ShowHomePresenterInterface;
use App\Domain\Home\UseCase\ShowHome\ShowHomeResponse;
use App\Domain\User\UseCase\Register\RegisterUserPresenterInterface;
use App\Domain\User\UseCase\Register\RegisterUserResponse;

final class ShowHomeHtmlPresenter implements ShowHomePresenterInterface
{
    private ShowHomeHtmlViewModel $viewModel;

    public function present(ShowHomeResponse $response): void
    {
        $this->viewModel = new ShowHomeHtmlViewModel();
        $this->viewModel->username = $response->getConnectedUser()?->getUsername();
        $this->viewModel->roles = $response->getConnectedUser()?->getRoles();
    }

    public function viewModel(): ShowHomeHtmlViewModel
    {
        return $this->viewModel;
    }
}
