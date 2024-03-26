<?php

namespace App\Infrastructure\Symfony\Controller;

use App\Domain\Cart\Service\CartInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/cart/create", "cart_create")]
class FakeCartController extends AbstractController
{
    public function __construct(private readonly CartInterface $cartService) {}


    public function __invoke(): Response
    {
        $this->cartService->createCart();
        $this->cartService->addProduct('1', 'Product 1', 100, 1);
        return new Response('Cart created');
    }


}