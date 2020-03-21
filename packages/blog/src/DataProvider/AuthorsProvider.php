<?php

declare(strict_types=1);

namespace Pehapkari\Blog\DataProvider;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class AuthorsProvider
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @return string[]
     */
    public function provide(): array
    {
        return $this->parameterBag->get('authors') ?? [];
    }

    /**
     * @return string[]
     */
    public function provideById(int $id): array
    {
        return $this->provide()[$id] ?? null;
    }

    public function getCount(): int
    {
        return count($this->provide());
    }
}
