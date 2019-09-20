<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\Command\ImportVideosCommand;
use Pehapkari\Youtube\Exception\FileDataNotFoundException;
use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
use Pehapkari\Youtube\Sorter\ArrayByDateTimeSorter;
use Pehapkari\Youtube\ValueObject\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\PackageBuilder\Console\Command\CommandNaming;

final class VideoController extends AbstractController
{
    /**
     * @var mixed[]
     */
    private $youtubeVideos = [];

    /**
     * @var mixed[]
     */
    private $facebookVideos = [];

    /**
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    /**
     * @var ArrayByDateTimeSorter
     */
    private $arrayByDateTimeSorter;

    /**
     * @see ImportVideosCommand command
     * @param mixed[] $youtubeVideos
     * @param mixed[] $facebookVideos
     */
    public function __construct(
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator,
        ArrayByDateTimeSorter $arrayByDateTimeSorter,
        array $youtubeVideos = [],
        array $facebookVideos = []
    ) {
        $this->youtubeVideos = $youtubeVideos;
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
        $this->facebookVideos = $facebookVideos;
        $this->arrayByDateTimeSorter = $arrayByDateTimeSorter;
    }

    /**
     * @Route(path="/prehaj-si-prednasku/", name="videos")
     */
    public function videos(): Response
    {
        $this->ensureYoutubeDataExists();

        $meetupPlaylists = array_merge($this->youtubeVideos['meetups'], $this->facebookVideos['meetups']);

        // sort meetups by month
        $meetupPlaylists = $this->arrayByDateTimeSorter->sortByKey($meetupPlaylists, 'month');

        foreach ($meetupPlaylists as $key => $meetupPlaylist) {
            $meetupPlaylists[$key]['videos'] = $this->arrayToValueObjectHydrator->hydrateArraysToValueObject(
                $meetupPlaylist['videos'],
                Video::class
            );
        }

        return $this->render('videos/videos.twig', [
            'meetup_playlists' => $meetupPlaylists,
            'livestream_count' => count($this->getLivestreamVideos()),
            'meetup_count' => count($meetupPlaylists),
            'video_count' => $this->getVideoCount($meetupPlaylists),
        ]);
    }

    /**
     * @Route(path="/livestream/", name="livestream")
     */
    public function livestream(): Response
    {
        $this->ensureYoutubeDataExists();

        return $this->render('videos/livestream.twig', [
            'livestream_videos' => $this->getLivestreamVideos(),
        ]);
    }

    /**
     * @Route(path="/video/{slug}", name="video_detail")
     */
    public function videoDetail(string $slug): Response
    {
        $this->ensureYoutubeDataExists();

        return $this->render('videos/video_detail.twig', [
            'video' => $this->getVideoBySlug($slug),
        ]);
    }

    /**
     * @Route(path="/videos/php-prague", name="videos_php_prague")
     */
    public function videoPhpPrague(): Response
    {
        $this->ensureYoutubeDataExists();

        return $this->render('videos/videos_php_prague.twig', [
            'playlists' => $this->youtubeVideos['php_prague'],
        ]);
    }

    private function ensureYoutubeDataExists(): void
    {
        if ($this->youtubeVideos && isset($this->youtubeVideos['livestream'], $this->youtubeVideos['meetups'])) {
            return;
        }

        throw new FileDataNotFoundException(sprintf(
            'Youtube data not found. Generate data by "%s" command first',
            CommandNaming::classToName(ImportVideosCommand::class)
        ));
    }

    /**
     * @return Video[]
     */
    private function getLivestreamVideos(): array
    {
        $livestreamPlaylist = $this->youtubeVideos['livestream'];
        $livestreamPlaylist['videos'] = $this->arrayToValueObjectHydrator->hydrateArraysToValueObject(
            $livestreamPlaylist['videos'],
            Video::class
        );

        return $livestreamPlaylist['videos'];
    }

    /**
     * @param mixed[] $meetupPlaylists
     */
    private function getVideoCount(array $meetupPlaylists): int
    {
        $videoCount = 0;

        foreach ($meetupPlaylists as $meetupPlaylist) {
            $videoCount += count($meetupPlaylist['videos'] ?? []);
        }

        return $videoCount;
    }

    private function getVideoBySlug(string $videoSlug): Video
    {
        foreach ($this->youtubeVideos['php_prague'] as $playlist) {
            foreach ($playlist['videos'] as $videoData) {
                if ($videoData['slug'] === $videoSlug) {
                    return $this->arrayToValueObjectHydrator->hydrateArrayToValueObject($videoData, Video::class);
                }
            }
        }

        foreach ($this->youtubeVideos['meetups'] as $playlist) {
            foreach ($playlist['videos'] as $videoData) {
                if ($videoData['slug'] === $videoSlug) {
                    return $this->arrayToValueObjectHydrator->hydrateArrayToValueObject($videoData, Video::class);
                }
            }
        }

        foreach ($this->youtubeVideos['livestream']['videos'] as $videoData) {
            if ($videoData['slug'] === $videoSlug) {
                return $this->arrayToValueObjectHydrator->hydrateArrayToValueObject($videoData, Video::class);
            }
        }

        foreach ($this->facebookVideos['meetups'] as $playlist) {
            foreach ($playlist['videos'] as $videoData) {
                if ($videoData['slug'] === $videoSlug) {
                    return $this->arrayToValueObjectHydrator->hydrateArrayToValueObject($videoData, Video::class);
                }
            }
        }

        throw $this->createNotFoundException(sprintf("Video with slug '%s' not found", $videoSlug));
    }
}
