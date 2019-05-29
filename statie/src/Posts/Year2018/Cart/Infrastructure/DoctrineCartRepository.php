<?php declare(strict_types=1);

namespace Pehapkari\Statie\Posts\Year2018\Cart\Infrastructure;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Pehapkari\Statie\Posts\Year2018\Cart\Domain\Cart;
use Pehapkari\Statie\Posts\Year2018\Cart\Domain\CartNotFoundException;
use Pehapkari\Statie\Posts\Year2018\Cart\Domain\CartRepositoryInterface;

final class DoctrineCartRepository implements CartRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Cart $cart): void
    {
        $this->entityManager->persist($cart);
    }

    public function get(string $id): Cart
    {
        return $this->getThrowingException($id);
    }

    public function remove(string $id): void
    {
        $cart = $this->getThrowingException($id);
        $this->entityManager->remove($cart);
    }

    private function getThrowingException(string $id): Cart
    {
        $cart = $this->find($id);
        if ($cart instanceof Cart) {
            return $cart;
        }

        throw new CartNotFoundException();
    }

    private function find(string $id): ?Cart
    {
        return $this->entityManager->find(Cart::class, $id);
    }
}
