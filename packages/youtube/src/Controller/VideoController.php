<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VideoController extends AbstractController
{
    private VideoRepository $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * @Route(path="prehaj-si-prednasku", name="videos")
     */
    public function __invoke(): Response
    {
        return $this->render('videos/videos.twig', [
            'recoded_meetups' => $this->videoRepository->getRecordedMeetups(),
            'livestream_count' => $this->videoRepository->getLivestreamVideosCount(),
            'meetup_count' => $this->videoRepository->getRecordedMeetupsCount(),
            'video_count' => $this->videoRepository->getMeetupVideosCount(),
            'php_prague_count' => $this->videoRepository->getPhpPragueVideosCount(),
        ]);
    }
}
