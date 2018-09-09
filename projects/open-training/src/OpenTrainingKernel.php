<?php declare(strict_types=1);

namespace OpenTraining;

use Iterator;
use OpenProject\AutoDiscovery\DependencyInjection\CompilerPass\AutoDiscoveryCompilerPass;
use OpenProject\AutoDiscovery\Routing\AnnotationRoutesAutodiscover;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class OpenTrainingKernel extends BaseKernel
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
        $this->configureContainerFlex($containerBuilder, $loader);

        $containerBuilder->addCompilerPass(new AutoDiscoveryCompilerPass());
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();

        // Symfony Flex
        $possibleRoutingPaths = [
            $this->getProjectDir() . '/config/{routes}*',
            $this->getProjectDir() . '/config/{routes}/' . $this->environment . '/**/*',
            $this->getProjectDir() . '/config/{routes}',
        ];
        foreach ($possibleRoutingPaths as $possibleRoutingDir) {
            $routeCollectionBuilder->import($possibleRoutingDir . self::CONFIG_EXTENSIONS, '/', 'glob');
        }
    }

    private function configureContainerFlex(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $containerBuilder->setParameter('container.dumper.inline_class_loader', true);

        // Symfony Flex
        $possibleServicePaths = [
            $this->getProjectDir() . '/config/{packages}/*',
            $this->getProjectDir() . '/config/{packages}/',
            $this->getProjectDir() . '/config/services',
            $this->getProjectDir() . '/config/{services}_',
            $this->getProjectDir() . '/packages/*/src/config/*',
        ];
        foreach ($possibleServicePaths as $possibleServicePath) {
            $loader->load($possibleServicePath . self::CONFIG_EXTENSIONS, 'glob');
        }
    }
}
