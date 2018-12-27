<?php declare(strict_types=1);

namespace OpenTraining\DependencyInjection\CompilerPass;

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CorrectionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $containerBuilder): void
    {
        foreach ($containerBuilder->getDefinitions() as $name => $definition) {
            if (! Strings::contains($definition->getClass(), 'Propel')) {
                continue;
            }

            // remove vichy-upload buggy and unused services - https://github.com/dustin10/VichUploaderBundle/blob/df0f3341d140fbc5bfd10d76f2a1a209d240ae87/Resources/config/listener.xml#L17-L30
            $containerBuilder->removeDefinition($name);
        }
    }
}
