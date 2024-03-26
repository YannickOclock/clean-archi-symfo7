<?php

namespace App\Tests\Unit;

use App\Domain\Home\Entity\ConnectedUser;
use PHPUnit\Framework\TestCase;

class HomeConnectedUserTest extends TestCase
{
    public function testHomeConnectedUser()
    {
        $connectedUser = new ConnectedUser('john@doe.fr', ['ROLE_USER']);
        $this->assertEquals('john@doe.fr', $connectedUser->getUsername());
        $this->assertEquals(['ROLE_USER'], $connectedUser->getRoles());
    }
}