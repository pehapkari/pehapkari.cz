<?php

declare(strict_types=1);

namespace Pehapkari\Blog\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;
use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Generator\Generator;
use Symplify\Statie\Generator\RelatedItemsResolver;
use Symplify\Statie\HttpKernel\StatieKernel;

final class StatieFactory
{
    /**
     * @var Container
     */
    private $statieContainer;

    public function __construct()
    {
        $this->statieContainer = $this->createStatieKernel();
        $this->configure();
    }

    public function createGenerator(): Generator
    {
        return $this->statieContainer->get(Generator::class);
    }

    public function createRelatedItemsResolver(): RelatedItemsResolver
    {
        return $this->statieContainer->get(RelatedItemsResolver::class);
    }

    private function createStatieKernel(): Container
    {
        $statieKernel = new StatieKernel('dev', true);
        $statieKernel->setConfigs([__DIR__ . '/../../statie.yaml']);
        $statieKernel->boot();

        return $statieKernel->getContainer();
    }

    private function configure(): void
    {
        /** @var StatieConfiguration $statieConfiguration */
        $statieConfiguration = $this->statieContainer->get(StatieConfiguration::class);
        $statieConfiguration->setDryRun(true);
        $statieConfiguration->setSourceDirectory(__DIR__ . '/../../../../statie/source');
    }
}
