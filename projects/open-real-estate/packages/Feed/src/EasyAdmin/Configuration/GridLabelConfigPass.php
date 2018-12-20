<?php declare(strict_types=1);

namespace OpenRealEstate\Feed\EasyAdmin\Configuration;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use Nette\Utils\Strings;
use OpenRealEstate\Feed\EasyAdmin\PropertyLabelResolver;
use ReflectionProperty;

final class GridLabelConfigPass implements ConfigPassInterface
{
    /**
     * @var PropertyLabelResolver
     */
    private $propertyLabelResolver;

    public function __construct(PropertyLabelResolver $propertyLabelResolver)
    {
        $this->propertyLabelResolver = $propertyLabelResolver;
    }

    /**
     * @param mixed[] $backendConfig
     * @return mixed[]
     */
    public function process(array $backendConfig): array
    {
        if (! isset($backendConfig['entities'])) {
            return $backendConfig;
        }

        foreach ($backendConfig['entities'] as $key => $entityConfiguration) {
            // complete labels to form items

            if (!isset($entityConfiguration['list']['fields'])) {
                continue;
            }

            foreach ($entityConfiguration['list']['fields'] as $name => $field) {
                if ($field['label']=== null) {
                    $label = $this->propertyLabelResolver->resolveFromPropertyAndEntityConfiguration($name, $entityConfiguration);
                    if ($label === null) {
                        continue;
                    }

                    $backendConfig['entities'][$key]['list']['fields'][$name]['label'] = $label;
                }
            }
        }

        return $backendConfig;
    }
}
