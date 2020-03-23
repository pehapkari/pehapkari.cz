<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
use Pehapkari\Youtube\Repository\VideoRepository;
use Pehapkari\Youtube\Sorter\ArrayByDateTimeSorter;
use Pehapkari\Youtube\ValueObject\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VideoController extends AbstractController
{
    private VideoRepository $videosDataProvider;

    private ArrayByDateTimeSorter $arrayByDateTimeSorter;

    private ArrayToValueObjectHydrator $arrayToValueObjectHydrator;

    public function __construct(
        VideoRepository $videoRepository,
        ArrayByDateTimeSorter $arrayByDateTimeSorter,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ) {
        $this->videosDataProvider = $videoRepository;
        $this->arrayByDateTimeSorter = $arrayByDateTimeSorter;
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
    }

    /**
     * @Route(path="prehaj-si-prednasku", name="videos")
     */
    public function __invoke(): Response
    {
        $meetupPlaylists = array_merge(
            $this->videosDataProvider->provideYoutubeVideos()['meetups'],
            $this->videosDataProvider->provideFacebookVideos()['meetups']
        );

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
            'livestream_count' => $this->videosDataProvider->getLivestreamVideosCount(),
            'meetup_count' => count($meetupPlaylists),
            'video_count' => $this->videosDataProvider->getMeetupVideosCount(),
            'php_prague_count' => $this->videosDataProvider->getPhpPragueVideosCount(),
        ]);
    }
}
