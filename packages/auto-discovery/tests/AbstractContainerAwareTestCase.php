<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests;

use OpenProject\AutoDiscovery\Tests\DependencyInjection\AudiscoveryTestingKernel;
use PHPUnit\Framework\TestCase;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    use ContainerAwareTestCaseTrait;

    protected function getKernelClass(): string
    {
        return AudiscoveryTestingKernel::class;
    }
}
