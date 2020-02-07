<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\MeetupNaming;

final class MeetupNaming
{
    public function createMeetupTitleWithMonth(string $meetupTitle, string $playlistMonth): string
    {
        [$year, $month] = explode('-', $playlistMonth);

        $monthName = $this->getMonthNameFromNumber((int) $month);

        return $meetupTitle . ', ' . $monthName . ' ' . $year;
    }

    private function getMonthNameFromNumber(int $monthNumber): string
    {
        $numberToMonth = [1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'];

        return $numberToMonth[$monthNumber];
    }
}
