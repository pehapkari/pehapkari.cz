<?php declare(strict_types=1);

namespace OpenRealEstate;

use Iterator;
use OpenProject\AutoDiscovery\Doctrine\DoctrineEntityAutodiscover;
use OpenProject\AutoDiscovery\Routing\AnnotationRoutesAutodiscover;
use OpenProject\AutoDiscovery\Twig\TwigPathsAutodiscoverer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class OpenRealEstateKernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var string
     */
    public const CONFIG_EXTENSIONS = '.{yaml,yml}';

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
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        (new DoctrineEntityAutodiscover($containerBuilder))->autodiscover();
        (new TwigPathsAutodiscoverer($containerBuilder))->autodiscover();

        $this->configureContainerFlex($containerBuilder, $loader);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->configureRoutesFlex($routeCollectionBuilder);

        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();
    }

    private function configureContainerFlex(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $containerBuilder->setParameter('container.dumper.inline_class_loader', true);

        $possibleServicePaths = [
            $this->getProjectDir() . '/config/{packages}/*',
            $this->getProjectDir() . '/config/{packages}/' . $this->environment . '/**/*',
            $this->getProjectDir() . '/config/services',
            $this->getProjectDir() . '/config/{services}_' . $this->environment,
            $this->getProjectDir() . '/packages/*/src/config/*',
            $this->getProjectDir() . '/packages/*/config/*',
        ];
        foreach ($possibleServicePaths as $possibleServicePath) {
            $loader->load($possibleServicePath . self::CONFIG_EXTENSIONS, 'glob');
        }
    }

    private function configureRoutesFlex(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $possibleRoutingPaths = [
            $this->getProjectDir() . '/config/routes/*',
            $this->getProjectDir() . '/config/routes/' . $this->environment . '/**/*',
            $this->getProjectDir() . '/config/routes',
        ];

        foreach ($possibleRoutingPaths as $possibleRoutingDir) {
            $routeCollectionBuilder->import($possibleRoutingDir . self::CONFIG_EXTENSIONS, '/', 'glob');
        }
    }
}
