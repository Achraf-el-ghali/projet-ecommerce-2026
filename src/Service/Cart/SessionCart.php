<?php

declare(strict_types=1);

namespace App\Service\Cart;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionCart implements CartInterface
{
    private const CART_SESSION_KEY = '_cart';

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository
    ) {
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    private function getRawCart(): array
    {
        return $this->getSession()->get(self::CART_SESSION_KEY, []);
    }

    private function saveRawCart(array $cart): void
    {
        $this->getSession()->set(self::CART_SESSION_KEY, $cart);
    }

    public function add(Product $product, int $quantity = 1): void
    {
        if ($quantity <= 0) {
            return;
        }

        $cart = $this->getRawCart();
        $productId = $product->getId();

        if (null === $productId) {
            return;
        }

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $this->saveRawCart($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->getRawCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        $this->saveRawCart($cart);
    }

    public function getItems(): array
    {
        $cart = $this->getRawCart();
        if (empty($cart)) {
            return [];
        }

        $productIds = array_keys($cart);
        $products = $this->productRepository->findBy(['id' => $productIds]);

        $items = [];
        foreach ($products as $product) {
            $id = $product->getId();
            if (null !== $id && isset($cart[$id])) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $cart[$id]
                ];
            }
        }

        return $items;
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::CART_SESSION_KEY);
    }

    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->getItems() as $item) {
            $total += ($item['product']->getPrice() ?? 0.0) * $item['quantity'];
        }

        return $total;
    }
}
