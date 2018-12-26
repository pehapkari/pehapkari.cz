<?php declare(strict_types=1);

namespace OpenTraining\EasyAdmin\ConfigPass;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @todo extract to /packages
 */
final class ImagePropertyAutoconfigureConfigPass implements ConfigPassInterface
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

        $show = false;
        foreach ($backendConfig['entities'] as $alias => $entityConfig) {
//            $backendConfig = $this->processGridFields($backendConfig, $entityConfig, $alias, 'list');
//            $backendConfig = $this->processGridFields($backendConfig, $entityConfig, $alias, 'show');
//            dump($entityConfig['show']['fields']);
            foreach ($entityConfig['form']['fields'] as $name => $field) {
                if (in_array($name, ['image', 'imageUploadedAt'], true)) {
                    $show = true;
                    unset($backendConfig['entities'][$alias]['form']['fields'][$name]);
                }

                if ($name === 'imageFile') {
                    $backendConfig['entities'][$alias]['form']['fields'][$name]['type'] = File::class;
                }
            }

            if ($show) {
                dump($backendConfig['entities'][$alias]['form']);
                die;
            }
        }

        return $backendConfig;
    }

    /**
     * @param mixed[] $backendConfig
     * @param mixed[] $entityConfig
     * @return mixed[]
     */
    private function processGridFields(array $backendConfig, array $entityConfig, string $alias, string $section): array
    {
        foreach ($entityConfig[$section]['fields'] as $key => $field) {
            if ($field['fieldName'] === 'image') {
//                unset($backendConfig['entities'][$alias][$section]['fields'][$key]);
//
//                // retype all references
//                foreach ($field as $subkey => $value) {
//                    if ($value === 'image') {
//                        $field[$subkey] = 'imageFile';
//                    }
//                }
//
//                $field['type'] = 'file';
//
//                $backendConfig['entities'][$alias][$section]['fields']['imageFile'] = $field;

//                dump($entityConfig[$section]['fields']);
            }
        }

        return $backendConfig;
    }
}
