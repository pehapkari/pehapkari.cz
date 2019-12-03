<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Pehapkari\Youtube\DataProvider\VideosDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PhpPragueVideosController extends AbstractController
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
     * @Route(path="videos/php-prague", name="videos_php_prague")
     */
    public function __invoke(): Response
    {
        return $this->render('videos/videos_php_prague.twig', [
            'playlists' => $this->videosDataProvider->provideYoutubeVideos()['php_prague'],
        ]);
    }
}
