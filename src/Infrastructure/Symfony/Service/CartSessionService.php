<?php

namespace App\Infrastructure\Symfony\Service;

use App\Domain\Cart\Entity\Cart;
use App\Domain\Cart\Service\CartInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class CartSessionService implements CartInterface
{
    public function __construct(private RequestStack $requestStack) {}
    public function createCart(): Cart
    {
        // create session cart
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');
        if (!$cart) {
            $cart = new Cart();
            $session->set('cart', $cart);
        }
        return $cart;
    }

    public function removeCart(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove('cart');
    }

    public function addProduct(string $productId, string $productName, int $productPrice, int $quantity): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');
        $cart->addProduct($productId, $productName, $productPrice, $quantity);
    }

    public function removeProduct(string $productId): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');
        $cart->removeProduct($productId);
    }

    public function getTotal(): int
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');
        return $cart->getTotal();
    }

    public function getProductsCount(): int
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');
        dump($cart);
        return $cart->getProductsCount();
    }
}