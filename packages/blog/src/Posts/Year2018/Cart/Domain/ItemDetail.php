<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2018\Cart\Domain;

final class ItemDetail
{
    private string $productId;

    private int $amount;

    private Price $price;

    public function __construct(string $productId, Price $price, int $amount)
    {
        $this->productId = $productId;
        $this->amount = $amount;
        $this->price = $price;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }
}
