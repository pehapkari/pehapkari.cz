<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Repository;

use Pehapkari\Youtube\Command\ImportVideosCommand;
use Pehapkari\Youtube\Exception\FileDataNotFoundException;
use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
use Pehapkari\Youtube\ValueObject\Video;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class VideoRepository
{
    private ArrayToValueObjectHydrator $arrayToValueObjectHydrator;

    /**
     * @var mixed[]
     */
    private array $facebookVideos = [];

    /**
     * @var mixed[]
     */
    private array $youtubeVideos = [];

    /**
     * @param mixed[] $facebookVideos
     * @param mixed[] $youtubeVideos
     */
    public function __construct(
        array $facebookVideos,
        array $youtubeVideos,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ) {
        $this->facebookVideos = $facebookVideos;
        $this->youtubeVideos = $youtubeVideos;
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;

        $this->ensureYoutubeDataExists();
    }

    /**
     * @return mixed[]
     */
    public function provideYoutubeVideos(): array
    {
        return $this->youtubeVideos;
    }

    /**
     * @return mixed[]
     */
    public function provideFacebookVideos(): array
    {
        return $this->facebookVideos;
    }

    /**
     * @return Video[]
     */
    public function provideLivestreamVideos(): array
    {
        $livestreamPlaylist = $this->provideYoutubeVideos()['livestream'];
        $livestreamPlaylist['videos'] = $this->arrayToValueObjectHydrator->hydrateArraysToValueObject(
            $livestreamPlaylist['videos'],
            Video::class
        );

        return $livestreamPlaylist['videos'];
    }

    public function getLivestreamVideosCount(): int
    {
        return count($this->provideLivestreamVideos());
    }

    public function getMeetupVideosCount(): int
    {
        $meetups = array_merge($this->provideYoutubeVideos()['meetups'], $this->provideFacebookVideos()['meetups']);

        $videoCount = 0;
        foreach ($meetups as $meetup) {
            $videoCount += count($meetup['videos']);
        }

        return $videoCount;
    }

    /**
     * @return mixed[]
     */
    public function provideEventsWithVideos(): array
    {
        return array_merge(
            $this->provideYoutubeVideos()['php_prague'],
            $this->provideYoutubeVideos()['meetups'],
            $this->provideFacebookVideos()['meetups']
        );
    }

    /**
     * @return mixed[]
     */
    public function provideAllVideos(): array
    {
        $eventsWithVideos = $this->provideEventsWithVideos();

        $videos = [];
        foreach ($eventsWithVideos as $event) {
            $videos = array_merge($videos, $event['videos']);
        }

        return [...$videos, ...$this->provideLivestreamVideos()];
    }

    public function getPhpPragueVideosCount(): int
    {
        $phpPragueCount = 0;
        foreach ($this->provideYoutubeVideos()['php_prague'] as $phpPrague) {
            $phpPragueCount += count($phpPrague['videos']);
        }

        return $phpPragueCount;
    }

    private function ensureYoutubeDataExists(): void
    {
        if ($this->provideYoutubeVideos() && isset($this->provideYoutubeVideos()['livestream'], $this->provideYoutubeVideos()['meetups'])) {
            return;
        }

        throw new FileDataNotFoundException(sprintf(
            'Youtube data not found. Generate data by "%s" command first',
            CommandNaming::classToName(ImportVideosCommand::class)
        ));
    }
}
