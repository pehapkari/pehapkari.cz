<?php declare(strict_types=1);

namespace OpenProject\BetterEasyAdmin\ConfigPass;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * @see https://github.com/alterphp/EasyAdminExtensionBundle#exclude-fields-in-forms, just global
 */
final class GlobalExcludeFieldsConfigPass implements ConfigPassInterface
{
    /**
     * @var string
     */
    private const ENTITIES = 'entities';

    /**
     * @var string
     */
    private const FIELDS = 'fields';

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param mixed[] $backendConfig
     * @return mixed[]
     */
    public function process(array $backendConfig): array
    {
        // nothing configured
        if (! $this->parameterBag->has('easy_admin_exclude_fields')) {
            return $backendConfig;
        }

        if (! isset($backendConfig[self::ENTITIES])) {
            return $backendConfig;
        }

        foreach ($backendConfig[self::ENTITIES] as $alias => $entityConfig) {
            $backendConfig[self::ENTITIES][$alias] = $this->decorateForms($entityConfig);
        }

        return $backendConfig;
    }

    /**
     * @param mixed[] $entityConfig
     * @return mixed[]
     */
    private function decorateForms(array $entityConfig): array
    {
        foreach (['form', 'edit', 'new'] as $formFieldsSection) {
            $entityConfig = $this->processFormFields($entityConfig, $formFieldsSection);
        }

        return $entityConfig;
    }


    /**
     * @param mixed[] $entityConfig
     * @return mixed[]
     */
    private function processFormFields(array $entityConfig, string $section): array
    {
        // already set explicitly, nothing to change
        if (isset($entityConfig[$section][self::FIELDS])) {
            return $entityConfig;
        }

        $excludeFields = $this->parameterBag->get('easy_admin_exclude_fields');
        $entityConfig[$section]['exclude_fields'] = array_merge($entityConfig[$section]['exclude_fields'] ?? [], $excludeFields);

        return $entityConfig;
    }
}
