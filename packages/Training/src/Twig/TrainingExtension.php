<?php

declare(strict_types=1);

namespace Pehapkari\Training\Twig;

use Pehapkari\Training\Entity\TrainingTerm;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class TrainingExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('from_to', function (TrainingTerm $trainingTerm): string {
                // @note for 1-day trainings only
                return $trainingTerm->getStartDateTime()->format('Y-m-d H:i')
                    . ' - '
                    . $trainingTerm->getEndDateTime()->format('H:i');
            }),
        ];
    }
}
