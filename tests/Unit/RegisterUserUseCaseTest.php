<?php

namespace App\Tests\Unit;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Service\UserIsAlreadyRegistered;
use App\Domain\User\UseCase\Register\RegisterUserPresenterInterface;
use App\Domain\User\UseCase\Register\RegisterUserRequest;
use App\Domain\User\UseCase\Register\RegisterUserUseCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserUseCaseTest extends WebTestCase
{
    public function testRegisterUser(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userIsAlreadyRegistered = $this->createMock(UserIsAlreadyRegistered::class);
        $registerUserUseCase = new RegisterUserUseCase($userRepository, $userIsAlreadyRegistered);

        $registerRequest = new RegisterUserRequest();
        $registerRequest->id = 1;
        $registerRequest->email = 'john@doe.fr';
        $registerRequest->password = 'password';
        $registerRequest->lastName = 'Doe';
        $registerRequest->firstName = 'John';
        $registerRequest->isPosted = true;
        $registerRequest->violations = null;

        $user = User::createUser(
            $registerRequest->id,
            $registerRequest->email,
            $registerRequest->password,
            $registerRequest->firstName,
            $registerRequest->lastName
        );

        $userRepository->expects($this->once())
            ->method('add')
            ->with($user);

        $userIsAlreadyRegistered->expects($this->once())
            ->method('isSatisfiedBy')
            ->with($registerRequest->email)
            ->willReturn(false);

        $presenter = $this->createMock(RegisterUserPresenterInterface::class);

        $presenter->expects($this->once())
            ->method('present');

        $registerUserUseCase->execute($registerRequest, $presenter);

        $this->assertNull($registerRequest->violations);
        $this->assertSame($user->getEmail(), $registerRequest->email);
        $this->assertSame($user->getPassword(), $registerRequest->password);
        $this->assertSame($user->getFirstName(), $registerRequest->firstName);
        $this->assertSame($user->getLastName(), $registerRequest->lastName);
    }
}

