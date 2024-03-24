<?php

namespace App\Infrastructure\Symfony\Service;

use Symfony\Bundle\SecurityBundle\Security;

readonly class SecurityUserService
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

