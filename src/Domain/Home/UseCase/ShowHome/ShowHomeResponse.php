<?php

declare(strict_types=1);

namespace App\Domain\Home\UseCase\ShowHome;


use App\Domain\Home\Entity\ConnectedUser;

class ShowHomeResponse
{
    private array $menuCategories;
    private ?ConnectedUser $connectedUser;

    public function getMenuCategories(): array
    {
        return $this->menuCategories;
    }

    public function setMenuCategories(array $menuCategories): void
    {
        $this->menuCategories = $menuCategories;
    }

    public function getConnectedUser(): ?ConnectedUser
    {
        return $this->connectedUser;
    }

    public function setConnectedUser(?ConnectedUser $connectedUser): void
    {
        $this->connectedUser = $connectedUser;
    }
}
