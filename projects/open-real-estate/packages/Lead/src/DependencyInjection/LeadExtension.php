<?php declare(strict_types=1);

namespace OpenRealEstate\Lead\DependencyInjection;

// https://matthiasnoback.nl/2014/06/framework-independent-controllers-part-3/
// or this: http://www.ahmed-samy.com/symofny2-twig-multiple-domains-templating/
use Iterator;
use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Finder\Finder;

final class LeadExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        // ...
    }

    public function prepend(ContainerBuilder $containerBuilder): void
    {
        $entityDirectories = $this->getEntityDirectories($containerBuilder);

        $entityMappings = [];
        foreach ($entityDirectories as $entityDirectory) {
            $namespace = $this->detectNamespaceFromDirectory($entityDirectory);
            if (! $namespace) {
                continue;
            }

            $entityMappings[] = [
                'name' => $namespace,
                'type' => 'annotation',
                'dir' => $entityDirectory->getRealPath(),
                'prefix' => $namespace
            ];
        }

        if (! count($entityMappings)) {
            return;
        }

        $containerBuilder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => $entityMappings
            ]
        ]);
    }

    /**
     * @return SplFileInfo[]
     */
    private function getEntityDirectories(ContainerBuilder $containerBuilder): Iterator
    {
        $projectDir = $containerBuilder->getParameter('kernel.project_dir') . '/packages';

        return Finder::create()->directories()
            ->name('Entity')
            ->in($projectDir)
            ->getIterator();
    }

    private function detectNamespaceFromDirectory(SplFileInfo $entityDirectory): ?string
    {
        $filesInDirectory = glob($entityDirectory->getRealPath() . '/*.php');
        if (!count($filesInDirectory)) {
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
