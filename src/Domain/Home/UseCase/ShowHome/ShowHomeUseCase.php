<?php

namespace App\Domain\Home\UseCase\ShowHome;

use App\Domain\Home\Entity\ConnectedUser;
use App\Domain\Home\Service\SecurityUserInterface;

class ShowHomeUseCase implements ShowHomeUseCaseInterface
{
    public function __construct(private SecurityUserInterface $securityUserService) {}
    public function execute(ShowHomeRequest $request, ShowHomeResponse $response, ShowHomePresenterInterface $presenter): void
    {
        $connectedUser = ConnectedUser::createFromUserIdentifier(
            $this->securityUserService->getUserIdentifier(),
            $this->securityUserService->getRoles()
        );
        $response->setConnectedUser($connectedUser);
        $presenter->present($response);
    }
}