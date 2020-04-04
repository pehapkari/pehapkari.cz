<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObjectFactory;

use Pehapkari\Youtube\ValueObject\RecordedMeetup;
use Pehapkari\Youtube\ValueObject\Video;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;

final class RecordedMeetupFactory
{
    private ArrayToValueObjectHydrator $arrayToValueObjectHydrator;

    public function __construct(ArrayToValueObjectHydrator $arrayToValueObjectHydrator)
    {
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
    }

    /**
     * @param mixed[] $recordedMeetupData
     */
    public function createFromData(array $recordedMeetupData): RecordedMeetup
    {
        /** @var Video[] $videos */
        $videos = $this->arrayToValueObjectHydrator->hydrateArrays($recordedMeetupData['videos'], Video::class);

        /** @var RecordedMeetup $recordedMeetup */
        $recordedMeetup = $this->arrayToValueObjectHydrator->hydrateArray(
            $recordedMeetupData,
            RecordedMeetup::class
        );

        $recordedMeetup->setVideos($videos);

        return $recordedMeetup;
    }
}
