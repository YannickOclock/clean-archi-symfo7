<?php

namespace App\Tests\Unit;

use App\Domain\Home\Entity\ConnectedUser;
use App\Domain\Home\Repository\CategoryRepositoryInterface;
use App\Domain\Home\Service\SecurityUserInterface;
use App\Domain\Home\UseCase\ShowHome\ShowHomePresenterInterface;
use App\Domain\Home\UseCase\ShowHome\ShowHomeRequest;
use App\Domain\Home\UseCase\ShowHome\ShowHomeResponse;
use App\Domain\Home\UseCase\ShowHome\ShowHomeUseCase;
use PHPUnit\Framework\TestCase;

class ShowHomeUseCaseTest extends TestCase
{
    public function testShowHomeUseCase()
    {
        $securityUserService = new class() implements SecurityUserInterface {
            public function getUserIdentifier(): string
            {
                return 'john@doe.fr';
            }

            public function getRoles(): array
            {
                return ['ROLE_USER'];
            }
        };

        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $categoryRepository->expects($this->once())
            ->method('findAllMenuCategories')
            ->willReturn(['category1', 'category2']);

        $showHomeRequest = new ShowHomeRequest();
        $showHomeResponse = new ShowHomeResponse();

        $presenter = $this->createMock(ShowHomePresenterInterface::class);

        $presenter->expects($this->once())
            ->method('present')
            ->with($showHomeResponse);

        $showHomeUseCase = new ShowHomeUseCase($securityUserService, $categoryRepository);

        $showHomeUseCase->execute(
            $showHomeRequest,
            $showHomeResponse,
            $presenter);

        $this->assertEquals('john@doe.fr', $showHomeResponse->getConnectedUser()->getUsername());
        $this->assertEquals(['ROLE_USER'], $showHomeResponse->getConnectedUser()->getRoles());
        $this->assertEquals(['category1', 'category2'], $showHomeResponse->getMenuCategories());
    }
}