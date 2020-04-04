<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\ValueObjectFactory;

use Pehapkari\Youtube\ValueObject\RecordedConference;
use Pehapkari\Youtube\ValueObject\Video;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;

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
        $videos = $this->arrayToValueObjectHydrator->hydrateArrays($data['videos'], Video::class);

        /** @var RecordedConference $recordedConference */
        $recordedConference = $this->arrayToValueObjectHydrator->hydrateArray($data, RecordedConference::class);
        $recordedConference->setVideos($videos);

        return $recordedConference;
    }
}
