<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\Command\ImportVideosCommand;
use Pehapkari\Youtube\DataProvider\VideosDataProvider;
use Pehapkari\Youtube\Hydration\ArrayToValueObjectHydrator;
use Pehapkari\Youtube\ValueObject\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VideoDetailController extends AbstractController
{
    /**
     * @var ArrayToValueObjectHydrator
     */
    private $arrayToValueObjectHydrator;

    /**
     * @var VideosDataProvider
     */
    private $videosDataProvider;

    /**
     * @see ImportVideosCommand command
     */
    public function __construct(
        ArrayToValueObjectHydrator $arrayToValueObjectHydrator,
        VideosDataProvider $videosDataProvider
    ) {
        $this->arrayToValueObjectHydrator = $arrayToValueObjectHydrator;
        $this->videosDataProvider = $videosDataProvider;
    }

    /**
     * @Route(path="video/{slug}", name="video_detail")
     */
    public function __invoke(string $slug): Response
    {
        return $this->render('videos/video_detail.twig', [
            'video' => $this->getVideoBySlug($slug),
        ]);
    }

    private function getVideoBySlug(string $videoSlug): Video
    {
        $matchedVideo = $this->matchVideo($videoSlug);
        if ($matchedVideo instanceof Video) {
            return $matchedVideo;
        }

        if ($matchedVideo) {
            return $this->arrayToValueObjectHydrator->hydrateArrayToValueObject($matchedVideo, Video::class);
        }

        throw $this->createNotFoundException(sprintf("Video with slug '%s' not found", $videoSlug));
    }

    /**
     * @return mixed[]|Video|null
     */
    private function matchVideo(string $videoSlug)
    {
        foreach ($this->videosDataProvider->provideAllVideos() as $videoData) {
            if ($videoData instanceof Video) {
                if ($videoData->getSlug() === $videoSlug) {
                    return $videoData;
                }
            } elseif ($videoData['slug'] === $videoSlug) {
                return $videoData;
            }
        }

        return null;
    }
}
