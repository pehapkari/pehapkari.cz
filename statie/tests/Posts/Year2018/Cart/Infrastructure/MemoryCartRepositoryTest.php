<?php declare(strict_types=1);

namespace OpenTraining\Statie\Tests\Posts\Year2018\Cart\Infrastructure;

use OpenTraining\Statie\Posts\Year2018\Cart\Domain\CartRepository;
use OpenTraining\Statie\Posts\Year2018\Cart\Infrastructure\MemoryCartRepository;

final class MemoryCartRepositoryTest extends CartRepositoryTest
{
    protected function createRepository(): CartRepository
    {
        return new MemoryCartRepository();
    }
}
