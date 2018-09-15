<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Util;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class Filesystem
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * @return SplFileInfo[]
     */
    public function getTemplatesDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('templates');
    }

    /**
     * @return SplFileInfo[]
     */
    public function getEntityDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('Entity');
    }

    /**
     * @return string[]
     */
    private function getDirectories(): array
    {
        $parameterBag = $this->containerBuilder->getParameterBag();
        $projectDir = $parameterBag->resolveValue('%kernel.project_dir%');

        $possibleDirs = [$projectDir . '/src', $projectDir . '/templates', $projectDir . '/packages'];

        $dirs = [];
        foreach ($possibleDirs as $possibleDir) {
            if (file_exists($possibleDir)) {
                $dirs[] = $possibleDir;
            }
        }

        return $dirs;
    }

    /**
     * @return string[]
     */
    private function getDirectoriesInSourceByName(string $name): array
    {
        if (! $this->getDirectories()) {
            return [];
        }

        $finder = Finder::create()
            ->directories()
            ->name($name)
            ->in($this->getDirectories());

        return iterator_to_array($finder->getIterator());
    }
}
