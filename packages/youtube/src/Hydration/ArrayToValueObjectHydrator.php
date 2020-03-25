<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Hydration;

use DateTimeInterface;
use Nette\Utils\DateTime;
use ReflectionClass;
use ReflectionParameter;
use ReflectionType;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

final class ArrayToValueObjectHydrator
{
    private StringFormatConverter $stringFormatConverter;

    /**
     * @var FilesystemAdapter&CacheInterface
     */
    private CacheInterface $cache;

    public function __construct(StringFormatConverter $stringFormatConverter, FilesystemAdapter $filesystemAdapter)
    {
        $this->stringFormatConverter = $stringFormatConverter;
        $this->cache = $filesystemAdapter;
    }

    /**
     * @param mixed[] $data
     */
    public function hydrateArrayToValueObject(array $data, string $class): object
    {
        $arrayHash = md5(serialize($data) . $class);

        /** @var CacheItem $cachedItem */
        $cachedItem = $this->cache->getItem($arrayHash);
        if ($cachedItem->get() !== null) {
            return $cachedItem->get();
        }

        $argumets = [];

        $parameterReflections = $this->getConstructorParameterReflections($class);
        foreach ($parameterReflections as $parameterReflection) {
            $key = $this->stringFormatConverter->camelCaseToUnderscore($parameterReflection->name);

            $value = $data[$key] ?? '';

            if ($this->isDateTimeType($parameterReflection)) {
                $value = DateTime::from($data[$key]);
            }

            $argumets[] = $value;
        }

        $value = new $class(...$argumets);

        $cachedItem->set($value);
        $this->cache->save($cachedItem);

        return $value;
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
     * @return ReflectionParameter[]
     */
    private function getConstructorParameterReflections(string $class): array
    {
        $classReflection = new ReflectionClass($class);

        return $classReflection->getConstructor()->getParameters();
    }

    private function isDateTimeType(ReflectionParameter $reflectionParameter): bool
    {
        $parameterType = $reflectionParameter->getType();
        if ($parameterType === null) {
            return false;
        }

        /** @var ReflectionType $parameterType */
        $parameterTypeName = $parameterType->getName();

        return is_a($parameterTypeName, DateTimeInterface::class, true);
    }
}
