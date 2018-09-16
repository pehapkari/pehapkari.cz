<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Routing;

use OpenProject\AutoDiscovery\Contract\AutodiscovererInterface;
use OpenProject\AutoDiscovery\Util\Filesystem;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class AnnotationRoutesAutodiscover implements AutodiscovererInterface
{
    /**
     * @var RouteCollectionBuilder
     */
    private $routeCollectionBuilder;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(RouteCollectionBuilder $routeCollectionBuilder, ContainerBuilder $containerBuilder)
    {
        $this->routeCollectionBuilder = $routeCollectionBuilder;
        $this->filesystem = new Filesystem($containerBuilder);
    }

    public function autodiscover(): void
    {
        foreach ($this->filesystem->getControllerDirectories() as $controllerDirectoryFileInfo) {
            $this->routeCollectionBuilder->import($controllerDirectoryFileInfo->getRealPath(), '/', 'annotation');
        }
    }
}
