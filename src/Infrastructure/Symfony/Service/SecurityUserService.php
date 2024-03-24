<?php

namespace App\Infrastructure\Symfony\Service;

use App\Domain\Home\Service\SecurityUserInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class SecurityUserService implements SecurityUserInterface
{
    public function __construct(private Security $security) {}

    public function getUserIdentifier(): ?string
    {
        return $this->security->getUser()?->getUserIdentifier();
    }

    public function getRoles(): array
    {
        return $this->security->getUser()?->getRoles() ?? [];
    }
}

