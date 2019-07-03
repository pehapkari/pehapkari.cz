<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider\Channel;

use Pehapkari\Youtube\YoutubeApi;

final class DigitalSolutionsPlaylistsProvider
{
    /**
     * @var string
     */
    private const DIGITAL_SOLUTIONS_CHANNEL_ID = 'UCjpVnaRpr8uJq4O0qYwxqoA';

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

        $this->playlists = $this->youtubeApi->getPlaylistsByChannel(self::DIGITAL_SOLUTIONS_CHANNEL_ID);

        return $this->playlists;
    }
}
