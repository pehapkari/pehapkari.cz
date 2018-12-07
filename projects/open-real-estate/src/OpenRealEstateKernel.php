<?php declare(strict_types=1);

namespace OpenRealEstate;

use Iterator;
use OpenProject\AutoDiscovery\Doctrine\DoctrineEntityAutodiscover;
use OpenProject\AutoDiscovery\Flex\FlexLoader;
use OpenProject\AutoDiscovery\Routing\AnnotationRoutesAutodiscover;
use OpenProject\AutoDiscovery\Twig\TwigPathsAutodiscoverer;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireSinglyImplementedCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ConfigurableCollectorCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class OpenRealEstateKernel extends BaseKernel
{
    use MicroKernelTrait;
    use SimpleKernelTrait;

    /**
     * @var FlexLoader
     */
    private $flexLoader;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);
        $this->flexLoader = new FlexLoader();
    }

//    public function getCacheDir(): string
//    {
//        return $this->getProjectDir() . '/var/cache/' . $this->environment;
//    }
//
//    public function getLogDir(): string
//    {
//        return $this->getProjectDir() . '/var/log';
//    }

    public function registerBundles(): Iterator
    {
        return $this->flexLoader->loadBundlesFromFilePath(
            $this->getProjectDir() . '/config/bundles.php',
            $this->environment
        );
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        (new DoctrineEntityAutodiscover($containerBuilder))->autodiscover();
        (new TwigPathsAutodiscoverer($containerBuilder))->autodiscover();

        $this->flexLoader->loadConfigs($containerBuilder, $loader, $this->environment);

        $loader->load(__DIR__ . '/../../../packages/user/config/config.yaml');
        $loader->load(__DIR__ . '/../../../packages/user/config/config_multi.yaml');

        // @todo load multiusers config based on parameter > multi_user: true?
        // or extension?
        // put under OpenReality account
    }

    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder): void
    {
        $this->flexLoader->loadRoutes($routeCollectionBuilder, $this->getContainerBuilder(), $this->environment);

        (new AnnotationRoutesAutodiscover($routeCollectionBuilder, $this->getContainerBuilder()))->autodiscover();
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        // needs to be first, since it's adding new service definitions
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());

        // tests
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());

        $containerBuilder->addCompilerPass(new ConfigurableCollectorCompilerPass());

        // autowiring
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireSinglyImplementedCompilerPass());
    }
}
