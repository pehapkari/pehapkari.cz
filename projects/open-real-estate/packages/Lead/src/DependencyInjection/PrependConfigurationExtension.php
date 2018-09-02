<?php declare(strict_types=1);

namespace OpenRealEstate\Lead\Entity\DependencyInjection;

// https://matthiasnoback.nl/2014/06/framework-independent-controllers-part-3/
// or this: http://www.ahmed-samy.com/symofny2-twig-multiple-domains-templating/
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class PrependConfigurationExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    public function prepend(ContainerBuilder $containerBuilder)
    {
        dump($containerBuilder);
        die;

        $containerBuilder->prependExtensionConfig(

        );
        // ...
    }
}