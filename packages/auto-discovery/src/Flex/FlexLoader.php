<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Flex;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class FlexLoader
{
    /**
     * @var string
     */
    public const CONFIG_EXTENSIONS = '.{yaml,yml}';

    public function loadConfigs(ContainerBuilder $containerBuilder, LoaderInterface $loader, string $environment)
    {
        $projectDir = $containerBuilder->getParameter('kernel.project_dir');

        $containerBuilder->addResource(new FileResource($projectDir . '/config/bundles.php'));
        $containerBuilder->setParameter('container.dumper.inline_class_loader', true);

        $possibleServicePaths = [
            $projectDir . '/config/packages/*',
            $projectDir . '/config/packages/' . $environment . '/**/*',
            $projectDir . '/config/services',
            $projectDir . '/config/services_' . $environment,
            $projectDir . '/packages/*/src/config/*',
        ];
        foreach ($possibleServicePaths as $possibleServicePath) {
            $loader->load($possibleServicePath . self::CONFIG_EXTENSIONS, 'glob');
        }
    }

    public function loadRoutes(RouteCollectionBuilder $routeCollectionBuilder, ContainerBuilder $containerBuilder, string $environment): void
    {
        $projectDir = $containerBuilder->getParameter('kernel.project_dir');

        $possibleRoutingPaths = [
            $projectDir . '/config/routes/*',
            $projectDir . '/config/routes/' . $environment . '/**/*',
            $projectDir . '/config/routes',
        ];

        foreach ($possibleRoutingPaths as $possibleRoutingDir) {
            $routeCollectionBuilder->import($possibleRoutingDir . self::CONFIG_EXTENSIONS, '/', 'glob');
        }
    }
}
