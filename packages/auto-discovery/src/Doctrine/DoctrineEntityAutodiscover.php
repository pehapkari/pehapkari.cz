<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Doctrine;

use Iterator;
use OpenProject\AutoDiscovery\Contract\AutodiscovererInterface;
use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

final class DoctrineEntityAutodiscover implements AutodiscovererInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    public function autodiscover(): void
    {
        $entityMappings = [];
        foreach ($this->getEntityDirectories() as $entityDirectory) {
            $namespace = $this->detectNamespaceFromDirectory($entityDirectory);
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

    private function detectNamespaceFromDirectory(SplFileInfo $entityDirectory): ?string
    {
        $filesInDirectory = glob($entityDirectory->getRealPath() . '/*.php');
        if (! count($filesInDirectory)) {
            return null;
        }

        $entityFilePath = array_pop($filesInDirectory);

        return $this->detectNamespaceFromFilePath($entityFilePath);
    }

    /**
     * @see https://stackoverflow.com/a/7153243/1348344
     */
    private function detectNamespaceFromFilePath(string $filePath): string
    {
        include $filePath;

        $classes = get_declared_classes();
        $class = end($classes);

        $classParts = explode('\\', $class);
        unset($classParts[count($classParts) - 1]);

        return implode('\\', $classParts);
    }
}
