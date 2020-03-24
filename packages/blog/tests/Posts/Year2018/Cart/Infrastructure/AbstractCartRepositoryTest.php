<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Tests\Posts\Year2018\Cart\Infrastructure;

use Pehapkari\Blog\Posts\Year2018\Cart\Domain\Cart;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\CartDetail;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\CartNotFoundException;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\CartRepositoryInterface;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\ItemDetail;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\Price;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

abstract class AbstractCartRepositoryTest extends TestCase
{
    private CartRepositoryInterface $cartRepository;

    protected function setUp(): void
    {
        $this->cartRepository = $this->createRepository();
    }

    public function testAddAndGetSuccessfully(): void
    {
        $cart = $this->createCartWithItem('1');
        $this->cartRepository->add($cart);
        $this->flush();

        $foundCart = $this->cartRepository->get('1');
        Assert::assertEquals($this->getCartDetailWithItem(), $foundCart->calculate());
    }

    public function testAddAndRemoveSuccessfully(): void
    {
        $cart = $this->createCartWithItem('1');
        $this->cartRepository->add($cart);
        $this->flush();

        $this->cartRepository->remove('1');
        $this->flush();

        $this->expectException(CartNotFoundException::class);
        $this->cartRepository->get('1');
    }

    public function testAddedIsTheSameObject(): void
    {
        $empty = $this->createEmptyCart('1');
        $this->cartRepository->add($empty);
        $empty->add('1', new Price(10.0));
        $this->flush();

        $found = $this->cartRepository->get('1');
        Assert::assertEquals($this->getCartDetailWithItem(), $found->calculate());
    }

    public function testFlushAddedItemPersists(): void
    {
        $empty = $this->createEmptyCart('1');
        $this->cartRepository->add($empty);
        $this->flush();

        $foundEmpty = $this->cartRepository->get('1');
        $foundEmpty->add('1', new Price(10.0));
        $this->flush();

        $found = $this->cartRepository->get('1');
        Assert::assertEquals($this->getCartDetailWithItem(), $found->calculate());
    }

    public function testFlushRemovedItemPersists(): void
    {
        $empty = $this->createCartWithItem('1');
        $this->cartRepository->add($empty);
        $this->flush();

        $foundEmpty = $this->cartRepository->get('1');
        $foundEmpty->remove('1');
        $this->flush();

        $found = $this->cartRepository->get('1');
        Assert::assertEquals($this->getEmptyCartDetail(), $found->calculate());
    }

    public function testGetNotExistingCauseException(): void
    {
        $this->expectException(CartNotFoundException::class);

        $this->cartRepository->get('1');
    }

    public function testRemoveNotExistingCauseException(): void
    {
        $this->expectException(CartNotFoundException::class);

        $this->cartRepository->remove('1');
    }

    public function testAddTwoAndGetTwoSuccessfully(): void
    {
        $withItem = $this->createCartWithItem('1');
        $this->cartRepository->add($withItem);
        $empty = $this->createEmptyCart('2');
        $this->cartRepository->add($empty);
        $this->flush();

        $foundEmpty = $this->cartRepository->get('1');
        Assert::assertEquals($this->getCartDetailWithItem(), $foundEmpty->calculate());

        $foundEmpty = $this->cartRepository->get('2');
        Assert::assertEquals($this->getEmptyCartDetail(), $foundEmpty->calculate());
    }

    protected function flush(): void
    {
    }

    abstract protected function createRepository(): CartRepositoryInterface;

    private function createCartWithItem(string $id): Cart
    {
        $cart = new Cart($id);
        $cart->add('1', new Price(10), 1);

        return $cart;
    }

    private function getCartDetailWithItem(): CartDetail
    {
        $item = new ItemDetail('1', new Price(10), 1);

        return new CartDetail([$item], new Price(10));
    }

    private function createEmptyCart(string $id): Cart
    {
        return new Cart($id);
    }

    private function getEmptyCartDetail(): CartDetail
    {
        return new CartDetail([], new Price(0));
    }
}
