<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Repository;

use Pehapkari\ValueObject\Organizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;

final class OrganizerRepository
{
    private ParameterBagInterface $parameterBag;

    private ArrayToValueObjectHydrator $arrayToValueObjectHydrator;

    public function __construct(
        ParameterBagInterface $parameterBag,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ) {
        $this->parameterBag = $parameterBag;
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
    }

    /**
     * @return Organizer[]
     */
    public function fetchAll(): array
    {
        $organizersData = $this->parameterBag->get('organizers');

        return $this->arrayToValueObjectHydrator->hydrateArrays($organizersData, Organizer::class);
    }
}
