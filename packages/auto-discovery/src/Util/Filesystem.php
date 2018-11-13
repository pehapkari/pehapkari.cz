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
     * @return SplFileInfo[]
     */
    public function getControllerDirectories(): array
    {
        return $this->getDirectoriesInSourceByName('Controller');
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
            ->in($this->getDirectories())
            ->notPath('#tests#');

        return iterator_to_array($finder->getIterator());
    }

    /**
     * @return string[]
     */
    private function getDirectories(): array
    {
        $projectDir = $this->getProjectDir();

        $possibleDirs = [$projectDir . '/src', $projectDir . '/templates', $projectDir . '/packages', __DIR__ . '/../../../../packages'];

        $dirs = [];
        foreach ($possibleDirs as $possibleDir) {
            if (file_exists($possibleDir)) {
                $dirs[] = $possibleDir;
            }
        }

        return $dirs;
    }

    private function getProjectDir(): string
    {
        if ($this->isPHPUnit()) {
            // the least wtf way to get different %kernel.project_dir% for tests
            return realpath(__DIR__ . '/../../tests/KernelProjectDir');
        }

        $projectDir = $this->containerBuilder->getParameter('kernel.project_dir');
        return realpath($projectDir);
    }

    private function isPHPUnit(): bool
    {
        // defined by PHPUnit
        return defined('PHPUNIT_COMPOSER_INSTALL') || defined('__PHPUNIT_PHAR__');
    }
}
