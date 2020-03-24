<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2018\Cart\Domain;

final class CartDetail
{
    private Price $totalPrice;
    /**
     * @var ItemDetail[]
     */
    private array $items = [];

    /**
     * @param ItemDetail[] $items
     */
    public function __construct(array $items, Price $totalPrice)
    {
        $this->items = $items;
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return ItemDetail[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalPrice(): Price
    {
        return $this->totalPrice;
    }
}
