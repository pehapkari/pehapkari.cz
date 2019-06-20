<?php declare(strict_types=1);

namespace Pehapkari\Training\Twig;

use DateTimeInterface;
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
                if ($trainingTerm->getStartDateTime()->format('Y-m-d') === $trainingTerm->getEndDateTime()->format('Y-m-d')) {
                    // same day

                    return $trainingTerm->getStartDateTime()->format('j. n. Y H:i - ') .
                        $trainingTerm->getEndDateTime()->format('H:i');
                }

                // differnt day :(
            }),
        ];
    }
}
