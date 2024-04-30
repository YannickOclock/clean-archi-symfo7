<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\View;

use AllowDynamicProperties;
use App\Domain\TestPost\UseCase\ShowPosts\ShowPostsRequest;
use App\Presentation\Test\ShowTestHtmlViewModel;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ShowTestHtmlView
{
    private Environment $twig;

    public function __construct(
        Environment $twig
    ) {
        $this->twig = $twig;
    }

    public function generateView(
        ShowPostsRequest $showPostsRequest,
        ShowTestHtmlViewModel $viewModel
    ): Response {

        return new Response($this->twig->render(
            'test/index.html.twig',
            [
                'viewModel' => $viewModel
            ]
        ));
    }
}
