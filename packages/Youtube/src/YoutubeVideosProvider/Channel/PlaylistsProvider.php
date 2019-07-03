<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider\Channel;

use Pehapkari\Youtube\YoutubeApi;

final class PlaylistsProvider
{
    /**
     * @var mixed[]
     */
    private $playlistsByChannelId = [];

    /**
     * @var YoutubeApi
     */
    private $youtubeApi;

    public function __construct(YoutubeApi $youtubeApi)
    {
        $this->youtubeApi = $youtubeApi;
    }

    /**
     * @return mixed[]
     */
    public function provideForChannel(string $channelId): array
    {
        if (isset($this->playlistsByChannelId[$channelId])) {
            return $this->playlistsByChannelId[$channelId];
        }

        $this->playlistsByChannelId[$channelId] = $this->youtubeApi->getPlaylistsByChannel($channelId);

        return $this->playlistsByChannelId[$channelId];
    }
}
