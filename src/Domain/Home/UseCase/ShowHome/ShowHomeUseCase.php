<?php

namespace App\Domain\Home\UseCase\ShowHome;

use App\Domain\Cart\Service\CartInterface;
use App\Domain\Home\Entity\ConnectedUser;
use App\Domain\Home\Repository\CategoryRepositoryInterface;
use App\Domain\Home\Service\SecurityUserInterface;

readonly class ShowHomeUseCase implements ShowHomeUseCaseInterface
{
    public function __construct(
        private SecurityUserInterface $securityUserService,
        private CategoryRepositoryInterface $categoryRepository,
        private CartInterface $cartService
    ) {}

    public function execute(ShowHomeRequest $request, ShowHomeResponse $response, ShowHomePresenterInterface $presenter): void
    {
        // Partie utilisateur connecté

        $connectedUser = ConnectedUser::createFromUserIdentifier(
            $this->securityUserService->getUserIdentifier(),
            $this->securityUserService->getRoles()
        );
        $response->setConnectedUser($connectedUser);

        // Partie catégories du menu

        $categories = $this->categoryRepository->findAllMenuCategories();
        $response->setMenuCategories($categories);

        // Partie panier

        $cartProductsCount = $this->cartService->getProductsCount();
        $response->setCartProductsCount($cartProductsCount);

        $presenter->present($response);
    }
}