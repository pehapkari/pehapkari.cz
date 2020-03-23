<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Nette\Utils\Strings;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Youtube\Contract\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\ChannelList;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\PlaylistsProvider;

final class PehapkariMeetupsYoutubeVideosProvider implements YoutubeVideosProviderInterface
{
    private YoutubeApi $youtubeApi;

    private VideosFactory $videosFactory;

    private PlaylistsProvider $playlistsProvider;

    public function __construct(
        YoutubeApi $youtubeApi,
        VideosFactory $videosFactory,
        PlaylistsProvider $playlistsProvider
    ) {
        $this->youtubeApi = $youtubeApi;
        $this->videosFactory = $videosFactory;
        $this->playlistsProvider = $playlistsProvider;
    }

    public function getName(): string
    {
        return 'meetups';
    }

    /**
     * @return mixed[]
     */
    public function providePlaylists(): array
    {
        $playlistsData = $this->playlistsProvider->provideForChannel(ChannelList::PEHAPKARI_CHANNEL_ID);

        $playlists = [];

        foreach ($playlistsData['items'] as $playlistItemData) {
            $videosInPlaylistData = $this->youtubeApi->getVideosByPlaylistId($playlistItemData['id']);

            $playlistTitle = $playlistItemData['snippet']['title'];
            if (Strings::match($playlistTitle, '#(livestream|php(\s+)?prague)#i')) {
                continue;
            }

            $playlists[] = [
                'title' => $playlistTitle,
                'videos' => $this->videosFactory->createVideos($videosInPlaylistData),
                'month' => $this->resolvePlaylistMonth($playlistItemData['snippet']['title']),
            ];
        }

        return $playlists;
    }

    private function resolvePlaylistMonth(string $playlistTitle): string
    {
        $match = Strings::match($playlistTitle, '#\, (?<month>\w+) (?<year>\d+)$#u');
        if (! isset($match['month']) || ! isset($match['year'])) {
            throw new ShouldNotHappenException(sprintf('Complete month for playlist "%s"', $playlistTitle));
        }

        // replace Czech string month by number
        $numberToMonth = [
            1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec',
        ];

        $monthToNumber = array_flip($numberToMonth);

        $month = $monthToNumber[$match['month']] ?? null;
        $year = $match['year'];

        return $year . '-' . $month;
    }
}
