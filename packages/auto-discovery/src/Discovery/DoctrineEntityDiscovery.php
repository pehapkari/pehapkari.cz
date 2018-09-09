<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Discovery;

use Iterator;
use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

final class DoctrineEntityDiscovery
{
    public function processContainerBuilder(ContainerBuilder $containerBuilder): void
    {
        $entityDirectories = $this->getEntityDirectories($containerBuilder);

        $entityMappings = [];
        foreach ($entityDirectories as $entityDirectory) {
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

        $containerBuilder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => $entityMappings,
            ],
        ]);
    }

    /**
     * @return SplFileInfo[]
     */
    private function getEntityDirectories(ContainerBuilder $containerBuilder): Iterator
    {
        $dirs = [
            $containerBuilder->getParameter('kernel.project_dir') . '/src',
            $containerBuilder->getParameter('kernel.project_dir') . '/packages',
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
