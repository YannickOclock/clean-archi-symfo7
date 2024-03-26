<?php

namespace App\Domain\Cart\Entity;

class Cart {
    /** @var CartProduct[] $cartProducts */
    private array $cartProducts = [];

    public function addProduct(string $id, string $name, int $price, int $quantity): void
    {
        if($this->findProduct($id)) {
            $this->updateProduct($id, $quantity);
        } else {
            $this->cartProducts[] = new CartProduct($id, $name, $price, $quantity);
        }
    }

    private function updateProduct(string $productId, int $quantity): void
    {
        foreach ($this->cartProducts as $product) {
            if ($product->getId() === $productId) {
                $product->addQuantity($quantity);
                return;
            }
        }
    }

    private function findProduct(string $productId): bool
    {
        foreach ($this->cartProducts as $product) {
            if ($product->getId() === $productId) {
                return true;
            }
        }
        return false;
    }

    public function removeProduct(string $productId): void
    {
        foreach ($this->cartProducts as $key => $product) {
            if ($product->getId() === $productId) {
                unset($this->cartProducts[$key]);
                return;
            }
        }
    }

    /**
     * @return CartProduct[]
     */
    public function getCartProducts(): array
    {
        return $this->cartProducts;
    }

    public function getTotal(): int
    {
        $total = 0;
        foreach ($this->cartProducts as $product) {
            $total += $product->getPrice() * $product->getQuantity();
        }
        return $total;
    }

    public function getProductsCount(): int
    {
        // count all quantities
        $count = 0;
        foreach ($this->cartProducts as $product) {
            $count += $product->getQuantity();
        }
        return $count;
    }
}