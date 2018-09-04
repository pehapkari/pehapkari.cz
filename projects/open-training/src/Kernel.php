<?php declare(strict_types=1);

namespace App;

use Iterator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends BaseKernel
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
        // local packages
        $loader->load($this->getProjectDir() . '/packages/*/src/config/*' . self::CONFIG_EXTENSIONS, 'glob');

        $this->configureContainerFlex($containerBuilder, $loader);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $confDir = $this->getProjectDir() . '/config';

        $routeCollectionBuilder->import($confDir . '/{routes}/*' . self::CONFIG_EXTENSIONS, '/', 'glob');
        $routeCollectionBuilder->import(
            $confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTENSIONS,
            '/',
            'glob'
        );
        $routeCollectionBuilder->import($confDir . '/{routes}' . self::CONFIG_EXTENSIONS, '/', 'glob');
    }

    private function configureContainerFlex(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $containerBuilder->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTENSIONS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTENSIONS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTENSIONS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTENSIONS, 'glob');
    }
}
