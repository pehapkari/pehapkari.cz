<?php declare(strict_types=1);

namespace OpenRealEstate\Feed\EasyAdmin;

use Nette\Utils\Strings;
use ReflectionProperty;

final class PropertyLabelResolver
{
    /**
     * @param mixed $property
     * @param mixed[] $entityConfiguration
     */
    public function resolveFromPropertyAndEntityConfiguration($property, array $entityConfiguration): ?string
    {
        if (is_array($property)) {
            $property = $property['property'];
        }

        if (! isset($entityConfiguration['class'])) {
            return null;
        }

        $reflectionProperty = new ReflectionProperty($entityConfiguration['class'], $property);
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
