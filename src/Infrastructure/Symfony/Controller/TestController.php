<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\TestPost\UseCase\ShowPosts\ShowPostsRequest;
use App\Domain\TestPost\UseCase\ShowPosts\ShowPostsResponse;
use App\Domain\TestPost\UseCase\ShowPosts\ShowPostsUseCaseInterface;
use App\Infrastructure\Symfony\View\ShowTestHtmlView;
use App\Presentation\Test\ShowTestHtmlPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test', name: 'app_test')]
class TestController extends AbstractController
{
    public function __construct(
        private ShowTestHtmlView $showTestView,
        private ShowPostsUseCaseInterface $showPostsUseCase,
        private ShowTestHtmlPresenter $presenter,
    ) {
    }

    public function __invoke(Request $request, ShowPostsRequest $showPostsRequest, ShowPostsResponse $showPostsResponse): Response
    {
        $this->showPostsUseCase->execute($showPostsRequest, $showPostsResponse, $this->presenter);

        return $this->showTestView->generateView(
            $showPostsRequest,
            $this->presenter->viewModel()
        );
    }
}
