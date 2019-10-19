<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Hydration;

use DateTimeInterface;
use Nette\Utils\DateTime;
use ReflectionClass;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

final class ArrayToValueObjectHydrator
{
    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    public function __construct(StringFormatConverter $stringFormatConverter)
    {
        $this->stringFormatConverter = $stringFormatConverter;
    }

    /**
     * @param mixed[] $data
     */
    public function hydrateArrayToValueObject(array $data, string $class): object
    {
        $parameterReflections = $this->getConstructorParameterReflections($class);

        $argumets = [];
        foreach ($parameterReflections as $parameterReflection) {
            $key = $this->stringFormatConverter->camelCaseToUnderscore($parameterReflection->name);

            $value = $data[$key] ?? '';
            if ($parameterReflection->hasType()) {
                if (is_a($parameterReflection->getType()->getName(), DateTimeInterface::class, true)) {
                    $value = DateTime::from($data[$key]);
                }
            }

            $argumets[] = $value;
        }

        return new $class(...$argumets);
    }

    /**
     * @param mixed[][] $datas
     * @return object[]
     */
    public function hydrateArraysToValueObject(array $datas, string $class): array
    {
        $objects = [];
        foreach ($datas as $data) {
            $objects[] = $this->hydrateArrayToValueObject($data, $class);
        }

        return $objects;
    }

    /**
     * @return \ReflectionParameter[]
     */
    private function getConstructorParameterReflections(string $class): array
    {
        $classReflection = new ReflectionClass($class);

        return $classReflection->getConstructor()->getParameters();
    }
}
