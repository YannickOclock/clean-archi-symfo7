<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\User\UseCase\Register\RegisterUserRequest;
use App\Domain\User\UseCase\Register\RegisterUserResponse;
use App\Domain\User\UseCase\Register\RegisterUserUseCaseInterface;
use App\Infrastructure\Symfony\Service\RegisterUserDataValidator;
use App\Infrastructure\Symfony\View\RegisterHtmlView;
use App\Presentation\User\RegisterUserHtmlPresenter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/register', name: 'app_register')]
final class RegisterUserController extends AbstractController
{
    private RegisterHtmlView $registerView;
    private RegisterUserUseCaseInterface $registerUseCase;
    private RegisterUserHtmlPresenter $presenter;
    private RegisterUserDataValidator $registerUserDataValidator;

    public function __construct(
        RegisterHtmlView $registerView,
        RegisterUserUseCaseInterface $registerUseCase,
        RegisterUserHtmlPresenter $presenter,
        RegisterUserDataValidator $registerUserDataValidator
    ) {
        $this->registerView = $registerView;
        $this->registerUseCase = $registerUseCase;
        $this->presenter = $presenter;
        $this->registerUserDataValidator = $registerUserDataValidator;
    }

    public function __invoke(Request $request, RegisterUserRequest $registerUserRequest, RegisterUserResponse $registerUserResponse): Response
    {

        $registerUserData = $request->request->all();
        $isPosted = $registerUserData['register_user']['isPosted'] ?? null;
        if ($isPosted !== null) {
            $registerUserRequest->violations = $this->registerUserDataValidator->gerErrors($registerUserData['register_user'] ?? null);
            $registerUserRequest->isPosted = (bool)(int) $isPosted;
            $registerUserRequest->email = $registerUserData['register_user']['email'] ?? null;
            $registerUserRequest->password = $registerUserData['register_user']['password'] ?? null;
            $registerUserRequest->firstName = $registerUserData['register_user']['firstName'] ?? null;
            $registerUserRequest->lastName = $registerUserData['register_user']['lastName'] ?? null;
            $registerUserRequest->id = Uuid::v4()->toRfc4122();
        }

        $this->registerUseCase->execute($registerUserRequest, $registerUserResponse, $this->presenter);

        return $this->registerView->generateView(
            $registerUserRequest,
            $this->presenter->viewModel()
        );
    }
}
