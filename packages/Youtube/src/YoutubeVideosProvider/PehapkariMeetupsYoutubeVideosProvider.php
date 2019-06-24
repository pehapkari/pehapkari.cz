<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Nette\Utils\Strings;
use Pehapkari\Youtube\Contract\YoutubeVideosProvider\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\PehapkariPlaylistsProvider;

final class PehapkariMeetupsYoutubeVideosProvider implements YoutubeVideosProviderInterface
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
        VideosFactory $videosFactory,
        PehapkariPlaylistsProvider $pehapkariPlaylistsProvider
    ) {
        $this->youtubeApi = $youtubeApi;
        $this->videosFactory = $videosFactory;
        $this->pehapkariPlaylistsProvider = $pehapkariPlaylistsProvider;
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
        $playlistsData = $this->pehapkariPlaylistsProvider->provide();

        $playlists = [];

        foreach ($playlistsData['items'] as $playlistItemData) {
            $videosInPlaylistData = $this->youtubeApi->getVideosByPlaylistId($playlistItemData['id']);

            $playlistTitle = $playlistItemData['snippet']['title'];
            if (Strings::match($playlistTitle, '#(livestream|phpprague)#i')) {
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
        $match = Strings::match($playlistTitle, '#\, (?<month>\w+) (?<year>\d+)$#');
        if (! isset($match['month']) || ! isset($match['year'])) {
            return '';
        }
        // replace Czech string month by number
        $numberToMonth = [1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'];
        $monthToNumber = array_flip($numberToMonth);

        $month = $monthToNumber[$match['month']] ?? null;
        $year = $match['year'];

        if ($month && $year) {
            return $year . '-' . $month;
        }

        return '';
    }
}
