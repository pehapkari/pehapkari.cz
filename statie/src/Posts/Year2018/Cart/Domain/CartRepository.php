<?php declare(strict_types=1);

namespace OpenTraining\Statie\Posts\Year2018\Cart\Domain;

interface CartRepository
{
    public function add(Cart $cart): void;

    /**
     * @throws CartNotFoundException
     */
    public function get(string $id): Cart;

    /**
     * @throws CartNotFoundException
     */
    public function remove(string $id): void;
}
