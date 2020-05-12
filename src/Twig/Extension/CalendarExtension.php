<?php

declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use Pehapkari\Training\Entity\TrainingTerm;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @docs https://symfony.com/doc/current/templating/twig_extension.html
 */
final class CalendarExtension extends AbstractExtension
{
    /**
     * @see https://stackoverflow.com/a/40475070/1348344
     * @var string
     */
    private const GOOGLE_CALENDAR_TIME_FORMAT = 'Ymd\\THi00';

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('google_calendar_link', function (TrainingTerm $trainingTerm): string {
                return $trainingTerm->getStartDateTime()->format(self::GOOGLE_CALENDAR_TIME_FORMAT) .
                '/' .
                $trainingTerm->getEndDateTime()->format(self::GOOGLE_CALENDAR_TIME_FORMAT);
            }),
        ];
    }
}
