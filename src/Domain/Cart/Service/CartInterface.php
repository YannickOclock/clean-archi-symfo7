<?php

namespace App\Domain\Cart\Service;

use App\Domain\Cart\Entity\Cart;

interface CartInterface
{
    public function createCart(): Cart;
    public function removeCart(): void;
    public function addProduct(string $productId, string $productName, int $productPrice, int $quantity): void;
    public function removeProduct(string $productId): void;
    public function getTotal(): int;
    public function getProductsCount(): int;
}