<?php declare(strict_types=1);

namespace OpenProject\BetterEasyAdmin\ConfigPass;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Works best with:
 * - @see \OpenProject\BetterEasyAdmin\DependencyInjection\CompilerPass\CorrectionCompilerPass
 * - @see \OpenProject\BetterEasyAdmin\Entity\UploadableImageTrait
 */
final class ImagePropertyAutoconfigureConfigPass implements ConfigPassInterface
{
    /**
     * @var string
     */
    private const IMAGE_FILE = 'imageFile';

    /**
     * @var string
     */
    private const IMAGE = 'image';

    /**
     * @var string
     */
    private const FIELDS = 'fields';

    /**
     * @var string
     */
    private const ENTITIES = 'entities';

    /**
     * @var string
     */
    private const IMAGE_UPLOADED_AT = 'imageUploadedAt';

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
        if (! isset($backendConfig[self::ENTITIES])) {
            return $backendConfig;
        }

        foreach ($backendConfig[self::ENTITIES] as $alias => $entityConfig) {
            $entityConfig = $this->decorateForms($entityConfig);
            $backendConfig[self::ENTITIES][$alias] = $this->decorateGrids($entityConfig);
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
    private function decorateGrids(array $entityConfig): array
    {
        foreach (['list', 'show'] as $section) {
            $entityConfig = $this->processGridFields($entityConfig, $section);
        }

        return $entityConfig;
    }

    /**
     * @param mixed[] $entityConfig
     * @return mixed[]
     */
    private function processFormFields(array $entityConfig, string $section): array
    {
        if (! isset($entityConfig[$section][self::FIELDS])) {
            return $entityConfig;
        }

        foreach (array_keys($entityConfig[$section][self::FIELDS]) as $key) {
            if (in_array($key, [self::IMAGE, self::IMAGE_UPLOADED_AT], true)) {
                unset($entityConfig[$section][self::FIELDS][$key]);
            }
        }

        // has class proprety "imageFile"? - always display it
        if (class_exists($entityConfig['class']) && property_exists($entityConfig['class'], self::IMAGE_FILE)) {
            $entityConfig[$section][self::FIELDS][self::IMAGE_FILE] = $this->createImageFileFormField();
        }

        return $entityConfig;
    }

    /**
     * @param mixed[] $entityConfig
     * @return mixed[]
     */
    private function processGridFields(array $entityConfig, string $section): array
    {
        if (! isset($entityConfig[$section][self::FIELDS])) {
            return $entityConfig;
        }

        foreach (array_keys($entityConfig[$section][self::FIELDS]) as $key) {
            if (in_array($key, ['imageFile', self::IMAGE_UPLOADED_AT], true)) {
                unset($entityConfig[$section][self::FIELDS][$key]);
            }
        }

        // has class proprety "imageFile"? - always display it
        if (class_exists($entityConfig['class']) && property_exists($entityConfig['class'], self::IMAGE)) {
            $entityConfig[$section][self::FIELDS][self::IMAGE] = $this->createGridField();
        }

        return $entityConfig;
    }

    /**
     * @return mixed[]
     */
    private function createImageFileFormField(): array
    {
        return [
            'css_class' => '',
            'format' => null,
            'help' => null,
            'label' => 'Image',
            'type' => VichImageType::class,
            'fieldType' => VichImageType::class,
            'dataType' => null,
            'virtual' => true,
            'sortable' => false,
            'template' => null,
            'type_options' => [
                'required' => false,
            ],
            'form_group' => null,
            'columnName' => 'imageFile',
            'fieldName' => 'imageFile',
            'id' => false,
            'length' => null,
            'nullable' => false,
            'precision' => 0,
            'scale' => 0,
            'unique' => false,
            'property' => self::IMAGE_FILE,
        ];
    }

    /**
     * @return mixed[]
     */
    private function createGridField(): array
    {
        return [
            'css_class' => '',
            'format' => null,
            'help' => null,
            'label' => self::IMAGE,
            'type' => 'image',
            'fieldType' => 'text',
            'dataType' => 'image',
            'virtual' => false,
            'sortable' => true,
            'template' => 'easy_admin/vich_uploader_image.twig',
            'type_options' => [
                'required' => true,
            ],
            'form_group' => null,
            'fieldName' => 'image',
            'scale' => 0,
            'length' => 255,
            'unique' => false,
            'nullable' => false,
            'precision' => 0,
            'columnName' => 'image',
            'property' => 'image',
            'base_path' => $this->parameterBag->resolveValue('%image_uploads%'),
        ];
    }
}
