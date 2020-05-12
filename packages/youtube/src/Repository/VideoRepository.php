<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Repository;

use Pehapkari\Exception\VideoNotFoundException;
use Pehapkari\Youtube\ValueObject\LivestreamVideo;
use Pehapkari\Youtube\ValueObject\RecordedConference;
use Pehapkari\Youtube\ValueObject\RecordedMeetup;
use Pehapkari\Youtube\ValueObject\Video;
use Pehapkari\Youtube\ValueObjectFactory\RecordedConferenceFactory;
use Pehapkari\Youtube\ValueObjectFactory\RecordedMeetupFactory;
use Symplify\EasyHydrator\ArrayToValueObjectHydrator;

final class VideoRepository
{
    /**
     * @var RecordedMeetup[]
     */
    private array $recordedMeetups = [];

    /**
     * @var LivestreamVideo[]
     */
    private array $livestreamVideos = [];

    /**
     * @var RecordedConference[]
     */
    private array $recordedConferences = [];

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
        $this->recordedMeetups = $this->createRecordedMeetups(
            $facebookVideos,
            $recordedMeetupFactory,
            $youtubeVideos['meetups']
        );
        $this->livestreamVideos = $arrayToValueObjectHydrator->hydrateArrays(
            $youtubeVideos['livestream']['videos'],
            LivestreamVideo::class
        );

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
     * @throws VideoNotFoundException
     *
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

        $eventVideo = $this->findVideoInRecodedEvents($slug);
        if ($eventVideo !== null) {
            return $eventVideo;
        }

        throw new VideoNotFoundException($slug);
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
     * @return RecordedMeetup[]
     */
    private function createRecordedMeetups(
        array $facebookVideos,
        RecordedMeetupFactory $recordedMeetupFactory,
        array $meetups
    ): array {
        $recordedMeetups = [];

        foreach ($facebookVideos as $facebookMeetups) {
            foreach ($facebookMeetups as $facebookMeetupData) {
                $recordedMeetups[] = $recordedMeetupFactory->createFromData($facebookMeetupData);
            }
        }

        foreach ($meetups as $youtubeMeetupData) {
            $recordedMeetups[] = $recordedMeetupFactory->createFromData($youtubeMeetupData);
        }

        return $this->sortRecodedMeetupsByMonth($recordedMeetups);
    }

    /**
     * @param RecordedMeetup[] $recordedMeetups
     * @return RecordedMeetup[]
     */
    private function sortRecodedMeetupsByMonth(array $recordedMeetups): array
    {
        usort(
            $recordedMeetups,
            fn (RecordedMeetup $firstRecodedMeetup, RecordedMeetup $secondRecodedMeetup) => $secondRecodedMeetup->getMonth() <=> $firstRecodedMeetup->getMonth()
        );

        return $recordedMeetups;
    }

    private function findVideoInRecodedEvents(string $slug): ?Video
    {
        /** @var RecordedMeetup[]|RecordedConference[] $recodedEvents */
        $recodedEvents = [...$this->recordedMeetups, ...$this->recordedConferences];

        foreach ($recodedEvents as $recodedEvent) {
            foreach ($recodedEvent->getVideos() as $video) {
                if ($video->getSlug() !== $slug) {
                    continue;
                }

                return $video;
            }
        }

        return null;
    }
}
