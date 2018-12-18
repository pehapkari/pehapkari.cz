<?php declare(strict_types=1);

namespace OpenTraining;

use Iterator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Doctrine\DoctrineEntityMappingAutodiscoverer;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscover;
use Symplify\Autodiscovery\Twig\TwigPathAutodiscoverer;
use Symplify\FlexLoader\Flex\FlexLoader;

final class OpenTrainingKernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var FlexLoader
     */
    private $flexLoader;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $this->flexLoader = new FlexLoader($environment, $this->getProjectDir());
    }

    public function registerBundles(): Iterator
    {
        return $this->flexLoader->loadBundles();
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        (new DoctrineEntityMappingAutodiscoverer($containerBuilder))->autodiscover();
        (new TwigPathAutodiscoverer($containerBuilder))->autodiscover();

        $this->flexLoader->loadConfigs($containerBuilder, $loader, [
            __DIR__ . '/../../../packages/*/config/config', // root packages
            $this->getProjectDir() . '/packages/*/config/*', // project packages
        ]);
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();

        $this->flexLoader->loadRoutes($routeCollectionBuilder);
    }
}
