<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Exception\VideoNotFoundException;
use Pehapkari\Youtube\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VideoDetailController extends AbstractController
{
    private VideoRepository $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * @Route(path="video/{slug}", name="video_detail")
     */
    public function __invoke(string $slug): Response
    {
        try {
            return $this->render('videos/video_detail.twig', [
                'video' => $this->videoRepository->findBySlug($slug),
            ]);
        } catch (VideoNotFoundException $videoNotFoundException) {
            throw $this->createNotFoundException($videoNotFoundException->getMessage(), $videoNotFoundException);
        }
    }
}
