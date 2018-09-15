<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Twig_Environment;

final class TwigPathsAutodiscoveryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Twig_Environment
     */
    private $twigEnvironment;

    protected function setUp(): void
    {
        $this->twigEnvironment = $this->container->get('twig');
    }

    public function test(): void
    {
        $twigLoader = $this->twigEnvironment->getLoader();
        $this->assertInstanceOf(FilesystemLoader::class, $twigLoader);

        /** @var FilesystemLoader $twigLoader */
        $this->assertCount(3, $twigLoader->getPaths());

        $this->assertContains(
            realpath(__DIR__ . '/../KernelProjectDir/packages/ForTests/templates/'),
            $twigLoader->getPaths()
        );
    }
}
