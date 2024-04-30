<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\TestPost\Entity\Post;
use App\Infrastructure\Symfony\Repository\DoctrineORMPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/test/post/create', name: 'app_test_post_create')]
class TestPostCreateController extends AbstractController
{
    public function __construct(private DoctrineORMPostRepository $doctrineORMPostRepository
    ) {
    }

    public function __invoke(): Response
    {
        $post = new Post(
            Uuid::v4()->toRfc4122(),
            'Test Post',
            'This is a test post'
        );
        $this->doctrineORMPostRepository->add($post);
        
        return new Response('Test Post Created');
    }
}
