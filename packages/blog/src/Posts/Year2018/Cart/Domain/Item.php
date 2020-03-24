<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2018\Cart\Domain;

final class Item
{
    private int $generatedId;

    private string $productId;

    private int $amount;

    private Price $unitPrice;

    public function __construct(string $productId, Price $unitPrice, int $amount)
    {
        $this->checkAmount($amount);
        $this->productId = $productId;
        $this->amount = $amount;
        $this->unitPrice = $unitPrice;
    }

    public function toDetail(): ItemDetail
    {
        return new ItemDetail($this->productId, $this->unitPrice, $this->amount);
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function add(int $amount): void
    {
        $this->checkAmount($amount);
        $this->amount += $amount;
    }

    public function changeAmount(int $amount): void
    {
        $this->checkAmount($amount);
        $this->amount = $amount;
    }

    public function calculatePrice(): Price
    {
        return $this->unitPrice->multiply($this->amount);
    }

    public function getGeneratedId(): int
    {
        return $this->generatedId;
    }

    private function checkAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new AmountMustBePositiveException();
        }
    }
}
