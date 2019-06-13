<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Utils;

use DateTimeInterface;
use Nette\Utils\DateTime;

final class DateTimeUtils
{
    public static function getHourDifferenceBetweenDateTimes(
        DateTimeInterface $firstDateTime,
        DateTimeInterface $secondDateTime
    ): int {
        $diff = $secondDateTime->diff($firstDateTime);

        return ($diff->y * 365 * 24) + ($diff->d * 24) + $diff->h;
    }

    public static function getDayDifferenceFromNow(DateTimeInterface $dateTime): int
    {
        $diff = $dateTime->diff(DateTime::from('now'));

        return (int) (($diff->y * 365) + $diff->d + ($diff->h / 24));
    }
}
