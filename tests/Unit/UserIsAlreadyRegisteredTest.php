<?php

namespace App\Tests\Unit;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\UserIsAlreadyRegistered;

class UserIsAlreadyRegisteredTest extends \PHPUnit\Framework\TestCase
{
    public function testUserIsAlreadyRegistered()
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userRepository->expects($this->once())
            ->method('findOneByEmail')
            ->with('lea@doe.fr')
            ->willReturn(new User('1', 'john@doe.fr', 'password', 'John', 'Doe'));

        $userIsAlreadyRegistered = new UserIsAlreadyRegistered($userRepository);

        $result = $userIsAlreadyRegistered->isSatisfiedBy('lea@doe.fr');

        $this->assertTrue($result);
    }
}
