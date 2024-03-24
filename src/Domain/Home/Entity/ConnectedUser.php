<?php

namespace App\Domain\Home\Entity;

class ConnectedUser
{
    private ?string $username;
    private array $roles = [];

    public function __construct(?string $username, array $roles)
    {
        $this->username = $username;
        $this->roles = $roles;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public static function createFromUserIdentifier(?string $userIdentifier, array $roles): self
    {
        return new self($userIdentifier, $roles);
    }
}