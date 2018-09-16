<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Tests\DependencyInjection;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use OpenProject\AutoDiscovery\Doctrine\DoctrineEntityAutodiscover;
use OpenProject\AutoDiscovery\Routing\AnnotationRoutesAutodiscover;
use OpenProject\AutoDiscovery\Twig\TwigPathsAutodiscoverer;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class AudiscoveryTestingKernel extends Kernel
{
    use SimpleKernelTrait;
    use MicroKernelTrait;

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new FrameworkBundle(), new TwigBundle(), new DoctrineBundle()];
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config_test.yaml');

        (new DoctrineEntityAutodiscover($containerBuilder))->autodiscover();
        (new TwigPathsAutodiscoverer($containerBuilder))->autodiscover();
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();
    }
}
