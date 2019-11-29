<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\DataProvider\VideosDataProvider;
use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
use Pehapkari\Youtube\Sorter\ArrayByDateTimeSorter;
use Pehapkari\Youtube\ValueObject\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VideoController extends AbstractController
{
    /**
     * @var VideosDataProvider
     */
    private $videosDataProvider;

    /**
     * @var ArrayByDateTimeSorter
     */
    private $arrayByDateTimeSorter;

    /**
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    public function __construct(
        VideosDataProvider $videosDataProvider,
        ArrayByDateTimeSorter $arrayByDateTimeSorter,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ) {
        $this->videosDataProvider = $videosDataProvider;
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
