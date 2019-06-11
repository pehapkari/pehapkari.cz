<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Controller;

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
     * Get these data by running @see ImportVideosFromYoutubeCommand command
     * @param mixed[] $youtubeVideos
     */
    public function __construct(array $youtubeVideos = [])
    {
        $this->youtubeVideos = $youtubeVideos;
    }

    /**
     * @Route(path="/prehaj-si-prednasku/", name="videos")
     */
    public function videos(): Response
    {
        $this->ensureYoutubeDataExists();

        return $this->render('videos/videos.twig', [
            'meetup_playlists' => $this->youtubeVideos['meetup_playlists'],
            'livestream_playlist' => $this->youtubeVideos['livestream_playlist'],
        ]);
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
