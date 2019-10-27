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
     * @Route(path="prehaj-si-prednasku", name="videos")
     */
    public function __invoke(
        VideosDataProvider $videosDataProvider,
        ArrayByDateTimeSorter $arrayByDateTimeSorter,
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator
    ): Response {
        $meetupPlaylists = array_merge(
            $videosDataProvider->provideYoutubeVideos()['meetups'],
            $videosDataProvider->provideFacebookVideos()['meetups']
        );

        // sort meetups by month
        $meetupPlaylists = $arrayByDateTimeSorter->sortByKey($meetupPlaylists, 'month');

        foreach ($meetupPlaylists as $key => $meetupPlaylist) {
            $meetupPlaylists[$key]['videos'] = $arrayToValueObjectHydrator->hydrateArraysToValueObject(
                $meetupPlaylist['videos'],
                Video::class
            );
        }

        return $this->render('videos/videos.twig', [
            'meetup_playlists' => $meetupPlaylists,
            'livestream_count' => $videosDataProvider->getLivestreamVideosCount(),
            'meetup_count' => count($meetupPlaylists),
            'video_count' => $videosDataProvider->getMeetupVideosCount(),
            'php_prague_count' => $videosDataProvider->getPhpPragueVideosCount(),
        ]);
    }
}
