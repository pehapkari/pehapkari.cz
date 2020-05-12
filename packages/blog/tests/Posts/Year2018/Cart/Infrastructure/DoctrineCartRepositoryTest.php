<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Tests\Posts\Year2018\Cart\Infrastructure;

use Doctrine\ORM\EntityManagerInterface;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\Cart;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\CartRepositoryInterface;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\Item;
use Pehapkari\Blog\Posts\Year2018\Cart\Domain\Price;
use Pehapkari\Blog\Posts\Year2018\Cart\Infrastructure\DoctrineCartRepository;
use Pehapkari\Blog\Tests\Posts\Year2018\Cart\Utils\EntityManagerFactory;
use Pehapkari\Blog\Tests\Posts\Year2018\Cart\Utils\StaticConnectionManager;
use PHPUnit\Framework\Assert;

final class DoctrineCartRepositoryTest extends AbstractCartRepositoryTest
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $connection = StaticConnectionManager::createSqliteMemoryConnection();
        $this->entityManager = EntityManagerFactory::createEntityManager($connection, [Cart::class, Item::class]);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->getConnection()->close();
    }

    public function testItemsAreRemovedWithCart(): void
    {
        $cart = new Cart('1');
        $cart->add('1', new Price(10), 1);
        $repository = $this->createRepository();
        $repository->add($cart);
        $this->flush();

        $repository->remove('1');
        $this->flush();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->from(Item::class, 'i')
            ->select('i');
        $query = $queryBuilder->getQuery();
        $result = $query->getResult();
        Assert::assertCount(0, $result);
    }

    protected function createRepository(): CartRepositoryInterface
    {
        return new DoctrineCartRepository($this->entityManager);
    }

    protected function flush(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
