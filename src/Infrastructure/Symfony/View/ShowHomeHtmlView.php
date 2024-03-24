<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\View;

use AllowDynamicProperties;
use App\Domain\Home\UseCase\ShowHome\ShowHomeRequest;
use App\Domain\User\UseCase\Register\RegisterUserRequest;
use App\Infrastructure\Symfony\Form\RegisterUserType;
use App\Presentation\Home\ShowHomeHtmlViewModel;
use App\Presentation\User\RegisterUserHtmlViewModel;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

final class ShowHomeHtmlView
{
    private Environment $twig;
    private FormFactoryInterface $formFactory;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
    }

    public function generateView(
        ShowHomeRequest $showHomeRequest,
        ShowHomeHtmlViewModel $viewModel
    ): Response {

        return new Response($this->twig->render(
            'home/index.html.twig',
            [
                'viewModel' => $viewModel
            ]
        ));
    }
}
