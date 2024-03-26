<?php

namespace App\Tests\Unit;

use App\Domain\Cart\Entity\Cart;
use App\Domain\Cart\Entity\CartProduct;
use PHPUnit\Framework\TestCase;

class CartDomainTest extends TestCase
{
    public function testCart(): void
    {
        $cart = new Cart();
        $this->assertEquals(0, $cart->getTotal());
        $this->assertEquals(0, $cart->getProductsCount());

        $cart->addProduct('1', 'Product 1', 100, 1);
        $this->assertEquals(100, $cart->getTotal());
        $this->assertEquals(1, $cart->getProductsCount());

        $cart->addProduct('2', 'Product 2', 200, 2);
        $this->assertEquals(500, $cart->getTotal());
        $this->assertEquals(3, $cart->getProductsCount());

        $cart->addProduct('3', 'Product 1', 100, 1);
        $this->assertEquals(600, $cart->getTotal());
        $this->assertEquals(4, $cart->getProductsCount());

        $cart->removeProduct('2');
        $this->assertEquals(200, $cart->getTotal());
        $this->assertEquals(2, $cart->getProductsCount());

        $cart->removeProduct('1');
        $this->assertEquals(100, $cart->getTotal());
        $this->assertEquals(1, $cart->getProductsCount());

        $cart->removeProduct('3');
        $this->assertEquals(0, $cart->getTotal());
        $this->assertEquals(0, $cart->getProductsCount());
    }

    public function testGetProducts(): void
    {
        $cart = new Cart();
        $cart->addProduct('1', 'Product 1', 100, 1);
        $cart->addProduct('2', 'Product 2', 200, 2);
        $cart->addProduct('3', 'Product 3', 300, 3);

        $products = $cart->getCartProducts();
        $this->assertCount(3, $products);

        $this->assertEquals('1', $products[0]->getId());
        $this->assertEquals('Product 1', $products[0]->getName());
        $this->assertEquals(100, $products[0]->getPrice());
        $this->assertEquals(1, $products[0]->getQuantity());

        $this->assertEquals('2', $products[1]->getId());
        $this->assertEquals('Product 2', $products[1]->getName());
        $this->assertEquals(200, $products[1]->getPrice());
        $this->assertEquals(2, $products[1]->getQuantity());

        $this->assertEquals('3', $products[2]->getId());
        $this->assertEquals('Product 3', $products[2]->getName());
        $this->assertEquals(300, $products[2]->getPrice());
        $this->assertEquals(3, $products[2]->getQuantity());
    }

    public function testUpdateProduct(): void
    {
        $cart = new Cart();
        $cart->addProduct('1', 'Product 1', 100, 1);
        $cart->addProduct('2', 'Product 2', 200, 2);
        $cart->addProduct('3', 'Product 3', 300, 3);

        $cart->addProduct('1', 'Product 1', 100, 1);
        $products = $cart->getCartProducts();
        $this->assertCount(3, $products);
        $this->assertEquals(2, $products[0]->getQuantity());
    }
}