<?php declare(strict_types=1);

namespace OpenRealEstate;

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
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ConfigurableCollectorCompilerPass;

final class OpenRealEstateKernel extends BaseKernel
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

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
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
            $this->getProjectDir() . '/packages/*/src/config',
            $this->getProjectDir() . '/packages/*/config',
        ]);

        // load optional specific configs
        $loader->load(__DIR__ . '/../../../packages/user/config/config.yaml');
        $loader->load(__DIR__ . '/../../../packages/user/config/config_multi.yaml');
<<<<<<< HEAD
=======

        // @todo load multiusers config based on parameter > multi_user: true?
        // or extension?
        // put under OpenReality account
>>>>>>> split easy_admin_multi.yaml
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
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
