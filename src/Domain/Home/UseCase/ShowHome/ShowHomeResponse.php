<?php

declare(strict_types=1);

namespace App\Domain\Home\UseCase\ShowHome;


use App\Domain\Home\Entity\ConnectedUser;

final class ShowHomeResponse
{
    private ?ConnectedUser $connectedUser;

    public function getConnectedUser(): ?ConnectedUser
    {
        return $this->connectedUser;
    }

    public function setConnectedUser(?ConnectedUser $connectedUser): void
    {
        $this->connectedUser = $connectedUser;
    }
}
