<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests\KernelProjectDir\Controller;

use Symfony\Component\Routing\Annotation\Route;

final class SomeController
{
    /**
     * @Route(path="/also-works/", name="also-works")
     */
    public function some(): void
    {
    }
}
