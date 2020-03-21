<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Pehapkari\Youtube\Contract\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\Sorter\ArrayByDateTimeSorter;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\ChannelList;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\PlaylistsProvider;

final class PehapkariLivestreamYoutubeVideosProvider implements YoutubeVideosProviderInterface
{
    private YoutubeApi $youtubeApi;

    private VideosFactory $videosFactory;

    private PlaylistsProvider $playlistsProvider;

    private ArrayByDateTimeSorter $arraySorter;

    public function __construct(
        YoutubeApi $youtubeApi,
        PlaylistsProvider $playlistsProvider,
        VideosFactory $videosFactory,
        ArrayByDateTimeSorter $arraySorter
    ) {
        $this->videosFactory = $videosFactory;
        $this->playlistsProvider = $playlistsProvider;
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
        $playlistsData = $this->playlistsProvider->provideForChannel(ChannelList::PEHAPKARI_CHANNEL_ID);

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
