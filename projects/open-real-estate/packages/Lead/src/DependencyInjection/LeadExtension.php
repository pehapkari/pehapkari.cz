<?php declare(strict_types=1);

namespace OpenRealEstate\Lead\DependencyInjection;

// https://matthiasnoback.nl/2014/06/framework-independent-controllers-part-3/
// or this: http://www.ahmed-samy.com/symofny2-twig-multiple-domains-templating/
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class LeadExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    public function prepend(ContainerBuilder $containerBuilder)
    {

        $containerBuilder->prependExtensionConfig(
            'doctrine', [
                'orm' => [
                    'mappings' => [
                        'OpenRealEstate\Lead' => [
                            'type' => 'annotation',
                            'dir' => '%kernel.project_dir%/packages/Lead/src/Entity',
                            'prefix' => 'OpenRealEstate\Lead\Entity',
                        ]
                    ]
                ]
            ]
        );

        // or rahter ompilep ass?
//
        // ...
    }
}