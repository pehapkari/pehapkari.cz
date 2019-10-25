<?php

declare(strict_types=1);

namespace Pehapkari\DataProvider;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Pehapkari\ValueObject\Meetup;

final class NearestMeetupProvider
{
    /**
     * @var string
     */
    private const MEETUPS_URL = 'https://friendsofphp.org/api/meetups.json';

    public function provide(): ?Meetup
    {
        $meetupsContent = FileSystem::read(self::MEETUPS_URL);

        try {
            $meetupsJson = Json::decode($meetupsContent, Json::FORCE_ARRAY);
        } catch (JsonException $jsonException) {
            return null;
        }

        $meetupsData = $meetupsJson['meetups'];
        $meetups = $this->hydrateMeetups($meetupsData);

        $czechMeetups = $this->filterCzechFutureMeetups($meetups);
        if (count($czechMeetups) === 0) {
            return null;
        }

        $czechMeetups = $this->sortMeetupsByNearest($czechMeetups);
        return $czechMeetups[0];
    }

    /**
     * @param mixed[] $arrayMeetups
     * @return Meetup[]
     */
    private function hydrateMeetups(array $arrayMeetups): array
    {
        $objectMeetups = [];

        foreach ($arrayMeetups as $arrayMeetup) {
            $startDateTime = DateTime::from($arrayMeetup['start']);
            $objectMeetups[] = new Meetup(
                $arrayMeetup['name'],
                $startDateTime,
                $arrayMeetup['city'],
                $arrayMeetup['url'],
                $arrayMeetup['country']
            );
        }

        return $objectMeetups;
    }

    /**
     * @param Meetup[] $meetups
     * @return Meetup[]
     */
    private function filterCzechFutureMeetups(array $meetups): array
    {
        $nowDateTime = DateTime::from('now');

        return array_filter($meetups, function (Meetup $meetup) use ($nowDateTime): bool {
            if ($meetup->getCountry() !== 'Czech Republic') {
                return false;
            }

            // is future meetup
            return $meetup->getStartDateTime() > $nowDateTime;
        });
    }

    /**
     * @param Meetup[] $meetups
     * @return Meetup[]
     */
    private function sortMeetupsByNearest(array $meetups): array
    {
        usort($meetups, function (Meetup $firstMeetup, Meetup $secondMeetup): int {
            return $firstMeetup->getStartDateTime() <=> $secondMeetup->getStartDateTime();
        });

        return $meetups;
    }
}
