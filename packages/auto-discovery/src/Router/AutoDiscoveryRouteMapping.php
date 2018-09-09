<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Router;

use Nette\Utils\Strings;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class AutoDiscoveryRouteMapping
{
    /**
     * @var
     */
    private $routeCollectionBuilder;

    public function __construct(RouteCollectionBuilder $routeCollectionBuilder)
    {
        $this->routeCollectionBuilder = $routeCollectionBuilder;
    }

    public function load(ContainerBuilder $containerBuilder)
    {
        $controllerDirectories = $this->getControllerDirectories($containerBuilder);

        foreach ($controllerDirectories as $controllerDirectoryFileInfo) {
            $this->routeCollectionBuilder->import($controllerDirectoryFileInfo->getRealPath(), '/', 'annotation');
        }
    }

    /**
     * @return SplFileInfo[]
     */
    private function getControllerDirectories(ContainerBuilder $containerBuilder): array
    {
        $dirs = [
            $containerBuilder->getParameter('kernel.project_dir') . '/src',
            $containerBuilder->getParameter('kernel.project_dir') . '/packages',
        ];

        $controllerDirectories = Finder::create()->directories()
            ->name('Controller')
            ->in($dirs)
            ->getIterator();

        return iterator_to_array($controllerDirectories);
    }
}
