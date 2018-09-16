<?php declare(strict_types=1);

namespace OpenRealEstate;

use Iterator;
use OpenProject\AutoDiscovery\Doctrine\DoctrineEntityAutodiscover;
use OpenProject\AutoDiscovery\Flex\FlexLoader;
use OpenProject\AutoDiscovery\Routing\AnnotationRoutesAutodiscover;
use OpenProject\AutoDiscovery\Twig\TwigPathsAutodiscoverer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class OpenRealEstateKernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var FlexLoader
     */
    private $flexLoader;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->flexLoader = new FlexLoader();
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles(): Iterator
    {
        return $this->flexLoader->loadBundlesFromFilePath(
            $this->getProjectDir() . '/config/bundles.php',
            $this->environment
        );
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        (new DoctrineEntityAutodiscover($containerBuilder))->autodiscover();
        (new TwigPathsAutodiscoverer($containerBuilder))->autodiscover();

        $this->flexLoader->loadConfigs($containerBuilder, $loader, $this->environment);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->flexLoader->loadRoutes($routeCollectionBuilder, $this->getContainerBuilder(), $this->environment);

        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();
    }
}
