<?php

declare(strict_types=1);

namespace App\Service\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CartHandler
{
    public function __construct(
        #[Autowire(service: SessionCart::class)]
        private CartInterface $cart,
        private ProductRepository $productRepository
    ) {
    }

    public function addToCart(int $productId, int $quantity = 1): void
    {
        $product = $this->productRepository->find($productId);

        if (null === $product) {
            throw new \InvalidArgumentException('Le produit demandé n\'existe pas.');
        }

        $this->cart->add($product, $quantity);
    }

    public function removeFromCart(int $productId): void
    {
        $this->cart->remove($productId);
    }

    public function getCartItems(): array
    {
        return $this->cart->getItems();
    }

    public function getCartTotal(): float
    {
        return $this->cart->getTotal();
    }

    public function clearCart(): void
    {
        $this->cart->clear();
    }
}
