<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\Cart\Service\CartInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart/remove', name: 'cart_remove')]
class FakeRemoveCartController extends AbstractController
{
    public function __construct(private readonly CartInterface $cartService) {}

    public function __invoke(): Response
    {
        $this->cartService->removeCart();
        return new Response('Cart removed');
    }
}