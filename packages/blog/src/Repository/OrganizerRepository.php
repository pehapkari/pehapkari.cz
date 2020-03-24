<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Repository;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class OrganizerRepository
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
        return $this->parameterBag->get('organizers') ?? [];
    }
}
