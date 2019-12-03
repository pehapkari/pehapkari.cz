<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\DataProvider\VideosDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class LivestreamVideosController extends AbstractController
{
    /**
     * @var VideosDataProvider
     */
    private $videosDataProvider;

    public function __construct(VideosDataProvider $videosDataProvider)
    {
        $this->videosDataProvider = $videosDataProvider;
    }

    /**
     * @Route(path="livestreamy", name="livestream")
     */
    public function __invoke(): Response
    {
        return $this->render('videos/livestream.twig', [
            'livestream_videos' => $this->videosDataProvider->provideLivestreamVideos(),
        ]);
    }
}
