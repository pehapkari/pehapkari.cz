<?php declare(strict_types=1);

namespace OpenTraining\Statie\Tests\Posts\Year2018\Cart\Infrastructure;

use OpenTraining\Statie\Posts\Year2018\Cart\Domain\CartRepositoryInterface;
use OpenTraining\Statie\Posts\Year2018\Cart\Infrastructure\MemoryCartRepository;

final class MemoryCartRepositoryTest extends AbstractCartRepositoryTest
{
    protected function createRepository(): CartRepositoryInterface
    {
        return new MemoryCartRepository();
    }
}
