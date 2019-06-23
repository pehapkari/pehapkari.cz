<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider\Channel;

use Pehapkari\Youtube\YoutubeApi;

final class PehapkariPlaylistsProvider
{
    /**
     * @var string
     */
    private const PEHAPKARI_CHANNEL_ID = 'UCTBgI1P8xIn2pp2BBHbv5mg';

    /**
     * @var mixed[]
     */
    private $playlists = [];

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
    public function provide(): array
    {
        if ($this->playlists) {
            return $this->playlists;
        }

        $this->playlists = $this->youtubeApi->getPlaylistsByChannel(self::PEHAPKARI_CHANNEL_ID);

        return $this->playlists;
    }
}
