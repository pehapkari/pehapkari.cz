<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObjectFactory;

use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
use Pehapkari\Youtube\ValueObject\RecordedConference;
use Pehapkari\Youtube\ValueObject\Video;

final class RecordedConferenceFactory
{
    private ArrayToValueObjectHydrator $arrayToValueObjectHydrator;

    public function __construct(ArrayToValueObjectHydrator $arrayToValueObjectHydrator)
    {
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
    }

    /**
     * @param mixed[] $data
     */
    public function createFromData(array $data): RecordedConference
    {
        /** @var Video[] $videos */
        $videos = $this->arrayToValueObjectHydrator->hydrateArraysToValueObject($data['videos'], Video::class);

        /** @var RecordedConference $recordedConference */
        $recordedConference = $this->arrayToValueObjectHydrator->hydrateArrayToValueObject(
            $data,
            RecordedConference::class
        );

        $recordedConference->setVideos($videos);

        return $recordedConference;
    }
}
