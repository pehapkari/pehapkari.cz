<?php declare(strict_types=1);

namespace OpenRealEstate;

use Iterator;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\Autodiscovery\Doctrine\DoctrineEntityMappingAutodiscoverer;
use Symplify\Autodiscovery\Routing\AnnotationRoutesAutodiscover;
use Symplify\Autodiscovery\Twig\TwigPathAutodiscoverer;
use Symplify\FlexLoader\Flex\FlexLoader;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ConfigurableCollectorCompilerPass;

final class OpenRealEstateKernel extends Kernel
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
            $this->getProjectDir() . '/packages/*/config/config', // project packages
        ]);

        // load optional specific configs
        $loader->load(__DIR__ . '/../../../packages/user/config/config_multi.yaml');
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->flexLoader->loadRoutes($routeCollectionBuilder);

        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        // needs to be first, since it's adding new service definitions
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());

        $containerBuilder->addCompilerPass(new ConfigurableCollectorCompilerPass());

        // autowiring
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass(), PassConfig::TYPE_BEFORE_REMOVING);
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
