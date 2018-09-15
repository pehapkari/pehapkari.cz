<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Doctrine;

use Iterator;
use OpenProject\AutoDiscovery\Contract\AutodiscovererInterface;
use OpenProject\AutoDiscovery\Php\NamespaceDetector;
use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

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

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->namespaceDetector = new NamespaceDetector();
    }

    public function autodiscover(): void
    {
        $entityMappings = [];
        foreach ($this->getEntityDirectories() as $entityDirectory) {
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

    /**
     * @return SplFileInfo[]
     */
    private function getEntityDirectories(): Iterator
    {
        $dirs = [
            $this->containerBuilder->getParameter('kernel.project_dir') . '/src',
            $this->containerBuilder->getParameter('kernel.project_dir') . '/packages',
        ];

        return Finder::create()->directories()
            ->name('Entity')
            ->in($dirs)
            ->getIterator();
    }
}
