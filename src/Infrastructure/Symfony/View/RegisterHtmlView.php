<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\View;

use AllowDynamicProperties;
use App\Domain\User\UseCase\Register\RegisterUserRequest;
use App\Infrastructure\Symfony\Form\RegisterUserType;
use App\Presentation\User\RegisterUserHtmlViewModel;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

#[AllowDynamicProperties] final class RegisterHtmlView
{
    private Environment $twig;
    private FormFactoryInterface $formFactory;
    private RequestStack $requestStack;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack,
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->requestStack = $requestStack;
    }

    public function generateView(
        RegisterUserRequest $registerUserRequest,
        RegisterUserHtmlViewModel $viewModel
    ): Response {
        if (!$viewModel->violations && $registerUserRequest->isPosted) {

            // Code pour mettre en session
            /*foreach ($viewModel->violations as $violation) {
                $this->requestStack->getSession()->getFlashBag()->add('error', $violation);
            }*/

            // Pour rediriger vers une autre page
            // return new RedirectResponse($this->urlGenerator->generate('app_home'));

            return new Response($this->twig->render(
                'user/register_complete.html.twig',
                [
                    'viewModel' => $viewModel
                ]
            ));
        }

        $form = $this->formFactory->createBuilder(RegisterUserType::class, $registerUserRequest)->getForm();

        return new Response($this->twig->render(
            'user/register.html.twig',
            [
                'form' => $form->createView(),
                'viewModel' => $viewModel
            ]
        ));
    }
}
