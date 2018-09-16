<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests\KernelProjectDir\ForTests\Controller;

use Symfony\Component\Routing\Annotation\Route;

final class SomeController
{
    /**
     * @Route(path="/it-works/", name="it-works")
     */
    public function some(): void
    {
    }
}
