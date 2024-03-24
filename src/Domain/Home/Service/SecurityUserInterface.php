<?php

namespace App\Domain\Home\Service;

interface SecurityUserInterface
{
    public function getUserIdentifier(): ?string;
    public function getRoles(): array;
}