<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2018\Cart\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class Cart
{
    private string $id;

    /**
     * @var Item[]&Collection
     */
    private Collection $items;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->items = new ArrayCollection();
    }

    public function add(string $productId, Price $unitPrice, int $amount = 1): void
    {
        try {
            $item = $this->find($productId);
            $item->add($amount);
        } catch (ProductNotInCartException $productNotInCartException) {
            $this->items->add(new Item($productId, $unitPrice, $amount));
        }
    }

    public function remove(string $productId): void
    {
        $key = $this->findKey($productId);
        unset($this->items[$key]);
    }

    public function changeAmount(string $productId, int $amount): void
    {
        $item = $this->find($productId);
        $item->changeAmount($amount);
    }

    public function calculate(): CartDetail
    {
        $detailItems = $this->items->map(fn (Item $item): ItemDetail => $item->toDetail())->getValues();

        $prices = $this->items->map(fn (Item $item): Price => $item->calculatePrice())->getValues();

        $totalPrice = Price::sum($prices);

        return new CartDetail(array_values($detailItems), $totalPrice);
    }

    public function getId(): string
    {
        return $this->id;
    }

    private function find(string $productId): Item
    {
        foreach ($this->items as $item) {
            if ($item->getProductId() === $productId) {
                return $item;
            }
        }

        throw new ProductNotInCartException();
    }

    private function findKey(string $productId): int
    {
        foreach ($this->items as $key => $item) {
            if ($item->getProductId() === $productId) {
                return $key;
            }
        }

        throw new ProductNotInCartException();
    }
}
