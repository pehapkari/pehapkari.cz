<?php declare(strict_types=1);

namespace OpenRealEstate\Feed\EasyAdmin\Configuration;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use Nette\Utils\Strings;
use ReflectionProperty;

final class ParamLabelConfigPass implements ConfigPassInterface
{
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
                $label = $this->resolveLabelFromPropertyNameComment($field, $entityConfiguration);
                if ($label === null) {
                    continue;
                }

                $entityConfiguration['form']['fields'][$subKey] = [
                    'property' => $field,
                    'label' => $label,
                ];
            }

            $backendConfig['entities'][$key] = $entityConfiguration;
        }

        return $backendConfig;
    }

    /**
     * @param mixed $field
     * @param mixed[] $entityConfiguration
     */
    private function resolveLabelFromPropertyNameComment($field, array $entityConfiguration): ?string
    {
        if (! is_string($field)) {
            return null;
        }

        if (! isset($entityConfiguration['class'])) {
            return null;
        }

        $reflectionProperty = new ReflectionProperty($entityConfiguration['class'], $field);
        $match = Strings::match($reflectionProperty->getDocComment(), '# name="(?<label>.*?)"#');
        if (! isset($match['label'])) {
            return null;
        }

        // "some_name" => "Some name"
        $label = $match['label'];
        $label = Strings::replace($label, '#_#', ' ');

        return ucfirst($label);
    }
}
