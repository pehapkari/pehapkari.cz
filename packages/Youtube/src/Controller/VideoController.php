<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

use Facebook\Facebook;
use Nette\Utils\Json;
use Pehapkari\Marketing\Social\FacebookIds;
use Pehapkari\Youtube\Command\ImportVideosFromYoutubeCommand;
use Pehapkari\Youtube\Exception\FileDataNotFoundException;
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
     * @var Facebook
     */
    private $facebook;

    /**
     * Get these data by running @see ImportVideosFromYoutubeCommand command
     * @param mixed[] $youtubeVideos
     */
    public function __construct(Facebook $facebook, array $youtubeVideos = [])
    {
        $this->youtubeVideos = $youtubeVideos;
        $this->facebook = $facebook;
    }

    /**
     * @Route(path="/prehaj-si-prednasku/", name="videos")
     */
    public function videos(): Response
    {
        $this->prepareYoutubeVideos();

        $this->ensureYoutubeDataExists();

        return $this->render('videos/videos.twig', [
            'meetup_playlists' => $this->youtubeVideos['meetup_playlists'],
            'livestream_playlist' => $this->youtubeVideos['livestream_playlist'],
        ]);
    }

    private function prepareYoutubeVideos(): void
    {
        return;

        // https://developers.facebook.com/docs/graph-api/reference/page/video_lists/
        $endPoint = FacebookIds::PEHAPKARI_PAGE_ID . '/video_lists';
        $response = $this->facebook->get($endPoint);

        $data = Json::decode($response->getBody(), Json::FORCE_ARRAY)['data'];

        foreach ($data as $item) {
            // https://developers.facebook.com/docs/graph-api/reference/video-list/
            $endPoint = $item['id'] . '?fields=videos';
            $response = $this->facebook->get($endPoint);

            $data = Json::decode($response->getBody(), Json::FORCE_ARRAY);
        }
    }

    private function ensureYoutubeDataExists(): void
    {
        if ($this->youtubeVideos && isset($this->youtubeVideos['livestream_playlist'], $this->youtubeVideos['meetup_playlists'])) {
            return;
        }

        throw new FileDataNotFoundException(sprintf(
            'Youtube data not found. Generate data by "%s" command first',
            CommandNaming::classToName(ImportVideosFromYoutubeCommand::class)
        ));
    }
}
