<?php declare(strict_types=1);

namespace OpenRealEstate\Feed\EasyAdmin\Configuration;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use OpenRealEstate\Feed\EasyAdmin\PropertyLabelResolver;

final class FormLabelConfigPass implements ConfigPassInterface
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
            if (! isset($entityConfiguration['form']['fields'])) {
                continue;
            }

            foreach ($entityConfiguration['form']['fields'] as $subKey => $field) {
                $label = $this->propertyLabelResolver->resolveFromPropertyAndEntityConfiguration(
                    $field,
                    $entityConfiguration
                );
                if ($label === null) {
                    continue;
                }

                $backendConfig['entities'][$key]['form']['fields'][$subKey] = [
                    'property' => $field,
                    'label' => $label,
                ];
            }
        }

        return $backendConfig;
    }
}
