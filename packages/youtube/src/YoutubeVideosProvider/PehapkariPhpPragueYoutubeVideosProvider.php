<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Nette\Utils\Strings;
use Pehapkari\Youtube\Contract\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\ChannelList;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\PlaylistsProvider;

final class PehapkariPhpPragueYoutubeVideosProvider implements YoutubeVideosProviderInterface
{
    private YoutubeApi $youtubeApi;

    private VideosFactory $videosFactory;

    private PlaylistsProvider $playlistsProvider;

    public function __construct(
        YoutubeApi $youtubeApi,
        PlaylistsProvider $playlistsProvider,
        VideosFactory $videosFactory
    ) {
        $this->videosFactory = $videosFactory;
        $this->playlistsProvider = $playlistsProvider;
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
        $playlistsData = $this->playlistsProvider->provideForChannel(ChannelList::PEHAPKARI_CHANNEL_ID);

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
