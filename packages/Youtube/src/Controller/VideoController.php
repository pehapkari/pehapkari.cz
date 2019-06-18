<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\Command\ImportVideosCommand;
use Pehapkari\Youtube\Exception\FileDataNotFoundException;
use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
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
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    /**
     * Get these data by running @param mixed[] $youtubeVideos
     * @see ImportVideosCommand command
     * @param mixed[] $youtubeVideos
     */
    public function __construct(ArrayToValueObjectHydrator $arrayToValueObjectHydrator, array $youtubeVideos = [])
    {
        $this->youtubeVideos = $youtubeVideos;
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
    }

    /**
     * @Route(path="/prehaj-si-prednasku/", name="videos")
     */
    public function videos(): Response
    {
        $this->ensureYoutubeDataExists();

        $meetupPlaylists = $this->youtubeVideos['meetup_playlists'];

        foreach ($meetupPlaylists as $key => $meetupPlaylist) {
            $meetupPlaylists[$key]['videos'] = $this->arrayToValueObjectHydrator->hydrateArraysToValueObject(
                $meetupPlaylist['videos'],
                Video::class
            );
        }

        return $this->render('videos/videos.twig', [
            'meetup_playlists' => $meetupPlaylists,
        ]);
    }

    /**
     * @Route(path="/livestream/", name="livestream")
     */
    public function livestream(): Response
    {
        $this->ensureYoutubeDataExists();

        $livestreamPlaylist = $this->youtubeVideos['livestream_playlist'];
        $livestreamPlaylist['videos'] = $this->arrayToValueObjectHydrator->hydrateArraysToValueObject(
            $livestreamPlaylist['videos'],
            Video::class
        );

        return $this->render('videos/livestream.twig', [
            'livestream_playlist' => $livestreamPlaylist,
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

    private function ensureYoutubeDataExists(): void
    {
        if ($this->youtubeVideos && isset($this->youtubeVideos['livestream_playlist'], $this->youtubeVideos['meetup_playlists'])) {
            return;
        }

        throw new FileDataNotFoundException(sprintf(
            'Youtube data not found. Generate data by "%s" command first',
            CommandNaming::classToName(ImportVideosCommand::class)
        ));
    }

    private function getVideoBySlug(string $videoSlug): Video
    {
        foreach ($this->youtubeVideos['meetup_playlists'] as $meetupPlaylist) {
            foreach ($meetupPlaylist['videos'] as $videoData) {
                if ($videoData['slug'] === $videoSlug) {
                    return $this->arrayToValueObjectHydrator->hydrateArrayToValueObject($videoData, Video::class);
                }
            }
        }

        foreach ($this->youtubeVideos['livestream_playlist']['videos'] as $videoData) {
            if ($videoData['slug'] === $videoSlug) {
                return $this->arrayToValueObjectHydrator->hydrateArrayToValueObject($videoData, Video::class);
            }
        }

        throw $this->createNotFoundException(sprintf("Video with slug '%s' not found", $videoSlug));
    }
}
