<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\Home\UseCase\ShowHome\ShowHomeRequest;
use App\Domain\Home\UseCase\ShowHome\ShowHomeResponse;
use App\Domain\Home\UseCase\ShowHome\ShowHomeUseCaseInterface;
use App\Domain\User\UseCase\Register\RegisterUserRequest;
use App\Domain\User\UseCase\Register\RegisterUserResponse;
use App\Domain\User\UseCase\Register\RegisterUserUseCaseInterface;
use App\Infrastructure\Symfony\Service\RegisterUserDataValidator;
use App\Infrastructure\Symfony\View\RegisterHtmlView;
use App\Infrastructure\Symfony\View\ShowHomeHtmlView;
use App\Presentation\Home\ShowHomeHtmlPresenter;
use App\Presentation\User\RegisterUserHtmlPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/', name: 'app_home')]
class HomeController extends AbstractController
{
    public function __construct(
        private ShowHomeHtmlView $showHomeView,
        private ShowHomeUseCaseInterface $showHomeUseCase,
        private ShowHomeHtmlPresenter $presenter,
    ) {
    }

    public function __invoke(Request $request, ShowHomeRequest $showHomeRequest, ShowHomeResponse $showHomeResponse): Response
    {
        $this->showHomeUseCase->execute($showHomeRequest, $showHomeResponse, $this->presenter);

        return $this->showHomeView->generateView(
            $showHomeRequest,
            $this->presenter->viewModel()
        );
    }
}
