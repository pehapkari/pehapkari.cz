<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Pehapkari\Youtube\Contract\YoutubeVideosProvider\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\Sorter\ArrayByDateTimeSorter;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\PehapkariPlaylistsProvider;

final class PehapkariLivestreamYoutubeVideosProvider implements YoutubeVideosProviderInterface
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

    /**
     * @var ArrayByDateTimeSorter
     */
    private $arraySorter;

    public function __construct(
        YoutubeApi $youtubeApi,
        PehapkariPlaylistsProvider $pehapkariPlaylistsProvider,
        VideosFactory $videosFactory,
        ArrayByDateTimeSorter $arraySorter
    ) {
        $this->videosFactory = $videosFactory;
        $this->pehapkariPlaylistsProvider = $pehapkariPlaylistsProvider;
        $this->youtubeApi = $youtubeApi;
        $this->arraySorter = $arraySorter;
    }

    public function getName(): string
    {
        return 'livestream';
    }

    /**
     * @return mixed[]
     */
    public function providePlaylists(): array
    {
        $playlistsData = $this->pehapkariPlaylistsProvider->provide();

        foreach ($playlistsData['items'] as $playlistItemData) {
            if ($playlistItemData['snippet']['title'] !== 'Twitch Livestream') {
                continue;
            }

            $videosData = $this->youtubeApi->getVideosByPlaylistId($playlistItemData['id']);
            $videos = $this->videosFactory->createVideos($videosData);

            return [
                'title' => 'Livestreamy',
                'videos' => $this->arraySorter->sortByKey($videos, 'month'),
            ];
        }

        return [];
    }
}
