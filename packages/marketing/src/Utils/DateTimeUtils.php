<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Utils;

use DateTimeInterface;

final class DateTimeUtils
{
    public function getHourDifferenceBetweenDateTimes(
        DateTimeInterface $firstDateTime,
        DateTimeInterface $secondDateTime
    ): int {
        $diff = $secondDateTime->diff($firstDateTime);

        return ($diff->y * 365 * 24) + ($diff->d * 24) + $diff->h;
    }
}
