<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Doctrine;

use OpenProject\AutoDiscovery\Contract\AutodiscovererInterface;
use OpenProject\AutoDiscovery\Util\Filesystem;
use OpenProject\AutoDiscovery\Util\NamespaceDetector;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DoctrineEntityAutodiscover implements AutodiscovererInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var NamespaceDetector
     */
    private $namespaceDetector;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->namespaceDetector = new NamespaceDetector();
        $this->filesystem = new Filesystem($containerBuilder);
    }

    /**
     * Needs to run before @see \Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass
     */
    public function autodiscover(): void
    {
        $entityMappings = [];
        foreach ($this->filesystem->getEntityDirectories() as $entityDirectory) {
            $namespace = $this->namespaceDetector->detectFromDirectory($entityDirectory);
            if (! $namespace) {
                continue;
            }

            $entityMappings[] = [
                'name' => $namespace, // required name
                'type' => 'annotation',
                'dir' => $entityDirectory->getRealPath(),
                'prefix' => $namespace,
                'is_bundle' => false, // performance
            ];
        }

        if (! count($entityMappings)) {
            return;
        }

        $this->containerBuilder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => $entityMappings,
            ],
        ]);
    }
}
