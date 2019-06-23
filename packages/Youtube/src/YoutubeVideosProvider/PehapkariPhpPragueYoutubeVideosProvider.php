<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Nette\Utils\Strings;
use Pehapkari\Youtube\Contract\YoutubeVideosProvider\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\PehapkariPlaylistsProvider;

final class PehapkariPhpPragueYoutubeVideosProvider implements YoutubeVideosProviderInterface
{
    /**
     * @var YoutubeApi
     */
    private $youtubeApi;

    /**
     * @var VideosFactory
     */
    private $videosFactory;

    /**
     * @var PehapkariPlaylistsProvider
     */
    private $pehapkariPlaylistsProvider;

    public function __construct(
        YoutubeApi $youtubeApi,
        PehapkariPlaylistsProvider $pehapkariPlaylistsProvider,
        VideosFactory $videosFactory
    ) {
        $this->videosFactory = $videosFactory;
        $this->pehapkariPlaylistsProvider = $pehapkariPlaylistsProvider;
        $this->youtubeApi = $youtubeApi;
    }

    public function getName(): string
    {
        return 'php_prague';
    }

    /**
     * @return mixed[][]
     */
    public function providePlaylists(): array
    {
        $playlistsData = $this->pehapkariPlaylistsProvider->provide();

        $playlists = [];
        foreach ($playlistsData['items'] as $playlistItemData) {
            if (! Strings::match($playlistItemData['snippet']['title'], '#PHP( )?Prague#i')) {
                continue;
            }

            $videosData = $this->youtubeApi->getVideosByPlaylistId($playlistItemData['id']);
            $videos = $this->videosFactory->createVideos($videosData);

            $playlists[] = [
                'title' => $playlistItemData['snippet']['title'],
                'videos' => $videos,
            ];
        }

        return $playlists;
    }
}
