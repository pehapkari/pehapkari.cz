<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Routing;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class AnnotationRoutesAutodiscover
{
    /**
     * @var RouteCollectionBuilder
     */
    private $routeCollectionBuilder;

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(RouteCollectionBuilder $routeCollectionBuilder, ContainerBuilder $containerBuilder)
    {
        $this->routeCollectionBuilder = $routeCollectionBuilder;
        $this->containerBuilder = $containerBuilder;
    }

    public function autodiscover(): void
    {
        foreach ($this->getControllerDirectories() as $controllerDirectoryFileInfo) {
            $this->routeCollectionBuilder->import($controllerDirectoryFileInfo->getRealPath(), '/', 'annotation');
        }
    }

    /**
     * @return SplFileInfo[]
     */
    private function getControllerDirectories(): array
    {
        $dirs = [
            $this->containerBuilder->getParameter('kernel.project_dir') . '/src',
            $this->containerBuilder->getParameter('kernel.project_dir') . '/packages',
        ];

        $controllerDirectories = Finder::create()->directories()
            ->name('Controller')
            ->in($dirs)
            ->getIterator();

        return iterator_to_array($controllerDirectories);
    }
}
