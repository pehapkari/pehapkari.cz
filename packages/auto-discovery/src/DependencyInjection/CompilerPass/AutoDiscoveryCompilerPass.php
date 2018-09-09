<?php declare(strict_types=1);

namespace OpenProject\AutoDiscovery\DependencyInjection\CompilerPass;

use OpenProject\AutoDiscovery\Doctrine\DoctrineEntityAutodiscover;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Inspiration: https://github.com/symfony/symfony/blob/e81285249b780a11ed209a79fa77c1f6ea6da67b/src/Symfony/Component/DependencyInjection/Compiler/MergeExtensionConfigurationPass.php#L44
 */
final class AutoDiscoveryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        // 1. autodiscovery entity directories with annotation
        (new DoctrineEntityAutodiscover($containerBuilder))->autodiscover();
    }
}
