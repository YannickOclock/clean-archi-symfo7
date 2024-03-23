<?php

namespace App\Tests\Domain;

use App\Domain\User\Entity\User;
use PHPUnit\Framework\TestCase;

class UserDomainTest extends TestCase
{
    public function testUser()
    {
        $user = User::createUser(
            '1',
            'john@doe.fr',
            'password',
            'John',
            'Doe'
        );

        $this->assertEquals('1', $user->getId());
        $this->assertEquals('john@doe.fr', $user->getEmail());
        $this->assertEquals('password', $user->getPassword());
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
    }
}


