<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Repository;

use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
use Pehapkari\Youtube\ValueObject\LivestreamVideo;
use Pehapkari\Youtube\ValueObject\RecordedConference;
use Pehapkari\Youtube\ValueObject\RecordedMeetup;
use Pehapkari\Youtube\ValueObject\Video;
use Pehapkari\Youtube\ValueObjectFactory\RecordedConferenceFactory;
use Pehapkari\Youtube\ValueObjectFactory\RecordedMeetupFactory;

final class VideoRepository
{
    /**
     * @var RecordedMeetup[]
     */
    private array $recordedMeetups = [];

    /**
     * @var LivestreamVideo[]
     */
    private $livestreamVideos = [];

    /**
     * @var RecordedConference[]
     */
    private $recordedConferences = [];

    /**
     * @param mixed[] $facebookVideos
     * @param mixed[] $youtubeVideos
     */
    public function __construct(
        array $facebookVideos,
        array $youtubeVideos,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator,
        RecordedMeetupFactory $recordedMeetupFactory,
        RecordedConferenceFactory $recordedConferenceFactory
    ) {
        foreach ($facebookVideos as $facebookMeetups) {
            foreach ($facebookMeetups as $facebookMeetupData) {
                $this->recordedMeetups[] = $recordedMeetupFactory->createFromData($facebookMeetupData);
            }
        }

        foreach ($youtubeVideos['meetups'] as $youtubeMeetupData) {
            $this->recordedMeetups[] = $recordedMeetupFactory->createFromData($youtubeMeetupData);
        }

        $this->recordedMeetups = $this->sortRecodedMeetupsByMonth($this->recordedMeetups);

        foreach ($youtubeVideos['livestream']['videos'] as $livestreamVideoData) {
            /** @var LivestreamVideo $livestreamVideo */
            $livestreamVideo = $arrayToValueObjectHydrator->hydrateArrayToValueObject(
                $livestreamVideoData,
                LivestreamVideo::class
            );

            $this->livestreamVideos[] = $livestreamVideo;
        }

        foreach ($youtubeVideos['php_prague'] as $recodedConference) {
            $this->recordedConferences[] = $recordedConferenceFactory->createFromData($recodedConference);
        }
    }

    /**
     * @return LivestreamVideo[]
     */
    public function getLivestreamVideos(): array
    {
        return $this->livestreamVideos;
    }

    public function getLivestreamVideosCount(): int
    {
        return count($this->getLivestreamVideos());
    }

    public function getMeetupVideosCount(): int
    {
        $videoCount = 0;
        foreach ($this->recordedMeetups as $recordedMeetup) {
            $videoCount += count($recordedMeetup->getVideos());
        }

        return $videoCount;
    }

    /**
     * @return Video|LivestreamVideo
     */
    public function findBySlug(string $slug): object
    {
        foreach ($this->livestreamVideos as $livestreamVideo) {
            if ($livestreamVideo->getSlug() !== $slug) {
                continue;
            }

            return $livestreamVideo;
        }

        /** @var RecordedMeetup[]|RecordedConference[] $recodedEvents */
        $recodedEvents = array_merge($this->recordedMeetups, $this->recordedConferences);

        foreach ($recodedEvents as $recodedEvent) {
            foreach ($recodedEvent->getVideos() as $video) {
                if ($video->getSlug() !== $slug) {
                    continue;
                }

                return $video;
            }
        }

        throw new ShouldNotHappenException(sprintf('Video for slug "%s" was not found', $slug));
    }

    public function getPhpPragueVideosCount(): int
    {
        $videoCount = 0;
        foreach ($this->recordedConferences as $recordedConference) {
            $videoCount += count($recordedConference->getVideos());
        }

        return $videoCount;
    }

    /**
     * @return RecordedMeetup[]
     */
    public function getRecordedMeetups(): array
    {
        return $this->recordedMeetups;
    }

    /**
     * @return RecordedConference[]
     */
    public function getRecordedConferences(): array
    {
        return $this->recordedConferences;
    }

    public function getRecordedMeetupsCount(): int
    {
        return count($this->recordedMeetups);
    }

    /**
     * @param RecordedMeetup[] $recordedMeetups
     * @return RecordedMeetup[]
     */
    private function sortRecodedMeetupsByMonth(array $recordedMeetups): array
    {
        usort($recordedMeetups, function (RecordedMeetup $firstRecodedMeetup, RecordedMeetup $secondRecodedMeetup) {
            return $secondRecodedMeetup->getMonth() <=> $firstRecodedMeetup->getMonth();
        });

        return $recordedMeetups;
    }
}
