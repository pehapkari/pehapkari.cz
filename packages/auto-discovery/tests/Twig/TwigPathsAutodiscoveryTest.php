<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests;

use OpenProject\AutoDiscovery\Twig\TwigPathsAutodiscoverer;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig_Environment;

/**
 * @see TwigPathsAutodiscoverer
 */
final class TwigPathsAutodiscoveryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var LoaderInterface
     */
    private $twigLoader;

    protected function setUp(): void
    {
        /** @var Twig_Environment $twigEnvironment */
        $twigEnvironment = $this->container->get('twig');
        $this->twigLoader = $twigEnvironment->getLoader();
    }

    public function test(): void
    {
        $this->assertInstanceOf(FilesystemLoader::class, $this->twigLoader);

        /** @var FilesystemLoader $twigLoader */
        $this->assertCount(3, $this->twigLoader->getPaths());

        $this->assertContains(
            realpath(__DIR__ . '/../KernelProjectDir/packages/ForTests/templates/'),
            $this->twigLoader->getPaths()
        );
    }
}
