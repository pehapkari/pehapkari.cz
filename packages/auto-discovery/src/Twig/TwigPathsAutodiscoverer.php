<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\Twig;

use OpenProject\AutoDiscovery\Contract\AutodiscovererInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class TwigPathsAutodiscoverer implements AutodiscovererInterface
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
        $twigLoaderFilesystemDefinition = $this->containerBuilder->getDefinition('twig.loader.filesystem');

        foreach ($this->getTemplateDirectories() as $templateDirectory) {
            $twigLoaderFilesystemDefinition->addMethodCall('addPath', [$templateDirectory->getRealPath()]);
        }
    }

    /**
     * @return SplFileInfo[]
     */
    private function getTemplateDirectories(): array
    {
        $dirs = [
            $this->containerBuilder->getParameter('kernel.project_dir'),
            $this->containerBuilder->getParameter('kernel.project_dir') . '/packages/',
        ];

        $finder = Finder::create()->directories()
            ->name('templates')
            ->in($dirs);

        return iterator_to_array($finder->getIterator());
    }
}
