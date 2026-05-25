<?php

declare(strict_types=1);

namespace App\Service\Cart;

use App\Entity\Product;

interface CartInterface
{
    public function add(Product $product, int $quantity = 1): void;

    public function remove(int $productId): void;

    public function getItems(): array;

    public function clear(): void;

    public function getTotal(): float;
}
