<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2018\Cart\Domain;

interface CartRepositoryInterface
{
    public function add(Cart $cart): void;

    public function get(string $id): Cart;

    public function remove(string $id): void;
}
