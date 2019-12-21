<?php

declare(strict_types=1);

namespace Pehapkari\Blog\DataProvider;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class AuthorsProvider
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

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

    public function getCount(): int
    {
        return count($this->provide());
    }
}
