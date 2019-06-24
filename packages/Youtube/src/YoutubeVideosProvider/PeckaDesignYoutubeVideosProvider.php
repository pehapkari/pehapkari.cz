<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Nette\Utils\Strings;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Youtube\Contract\YoutubeVideosProvider\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\YoutubeApi;

final class PeckaDesignYoutubeVideosProvider implements YoutubeVideosProviderInterface
{
    /**
     * @see https://www.youtube.com/playlist?list=PLtzY2tCed56eGLPdGzn0_Jq0L-PrVHTNU
     * @var string
     */
    private const PECKADESIGN_PLAYLIST_ID = 'PLtzY2tCed56eGLPdGzn0_Jq0L-PrVHTNU';

    /**
     * @var YoutubeApi
     */
    private $youtubeApi;

    /**
     * @var VideosFactory
     */
    private $videosFactory;

    public function __construct(YoutubeApi $youtubeApi, VideosFactory $videosFactory)
    {
        $this->youtubeApi = $youtubeApi;
        $this->videosFactory = $videosFactory;
    }

    /**
     * @return mixed[]
     */
    public function providePlaylists(): array
    {
        $playlistData = $this->youtubeApi->getVideosByPlaylistId(self::PECKADESIGN_PLAYLIST_ID);

        $videos = $this->videosFactory->createVideos($playlistData);

        $playlists = [];
        foreach ($videos as $video) {
            $playlistMonth = $this->resolvePlaylistMonth($video['title']);
            $meetupTitle = $this->resolveMeetupName($video);

            $video['title'] = $this->normalizeVideoTitle($video['title']);

            $uniqueHash = md5(Strings::webalize($playlistMonth . $meetupTitle));

            // group to playlist by meetup date
            $playlists[$uniqueHash]['title'] = $this->createMeetupTitleWithMonth($meetupTitle, $playlistMonth);
            $playlists[$uniqueHash]['videos'][] = $video;
            $playlists[$uniqueHash]['month'] = $playlistMonth;
        }

        return $playlists;
    }

    public function getName(): string
    {
        return 'meetups';
    }

    private function resolvePlaylistMonth(string $videoTitle): string
    {
        $match = Strings::match($videoTitle, '#[\d]{1,2}\.(\s+)?(?<month>[\d]{1,2}).(\s+)?(?<year>[\d]{4})#');
        if (isset($match['month']) && isset($match['year'])) {
            return $match['year'] . '-' . Strings::padLeft($match['month'], 2, '0');
        }

        if (Strings::match($videoTitle, '#A refactoring Journey – From Legacy to Laravel#')) {
            return '2017-11';
        }

        if (Strings::match(
            $videoTitle,
            '#Čtyři hlavní příčiny dysfunkčních návyků v týmu – Michal Abaffy#'
        )) {
            return '2019-04';
        }

        throw new ShouldNotHappenException(sprintf('Resolve playlist month for "%s"', $videoTitle));
    }

    /**
     * @param mixed[] $video
     */
    private function resolveMeetupName(array $video): string
    {
        $rank = $this->resolveMeetupRank($video['title'], $video['description']);

        return $rank . '. sraz přátel PHP v Brně';
    }

    private function normalizeVideoTitle(string $videoTitle): string
    {
        // normalize dashes
        $videoTitle = Strings::replace($videoTitle, '#–#', '-');

        // remove prefix
        $videoTitle = Strings::replace(
            $videoTitle,
            '#(Péhápkaři v Pecce: |Péhápkaři v Pecce: |Péhápkaři.cz - |Péhápkaři v Pecce: )#'
        );
        $videoTitle = Strings::replace($videoTitle, '# - Péhápkaři v Pecce#');

        // remove date
        $videoTitle = Strings::replace($videoTitle, '#( - )?\d{1,2}\.(\s+)?\d{1,2}\.(\s+)\d{4}?#');

        // make speaker first
        if (Strings::contains($videoTitle, 'A refactoring Journey - From Legacy to Laravel - Christopher Fuchs')) {
            $videoTitle = 'Christopher Fuchs - A refactoring Journey - From Legacy to Laravel';
        } elseif (Strings::contains($videoTitle, ' - ')) {
            [$talk, $speaker] = explode(' - ', $videoTitle);
            $videoTitle = $speaker . ' - ' . $talk;
        }

        return trim($videoTitle);
    }

    private function createMeetupTitleWithMonth(string $meetupTitle, string $playlistMonth): string
    {
        [$year, $month] = explode('-', $playlistMonth);

        $monthName = $this->getMonthNameFromNumber((int) $month);

        return $meetupTitle . ', ' . $monthName . ' ' . $year;
    }

    private function resolveMeetupRank(string $videoTitle, string $videoDescription): int
    {
        $match = Strings::match($videoDescription, '#Přednáška\s+z\s+(?<rank>\d+)#i');
        if (isset($match['rank'])) {
            return (int) $match['rank'];
        }

        if (Strings::match(
            $videoTitle,
            '#(A refactoring Journey – From Legacy to Laravel|Test Driven Development v praxi)#'
        )) {
            return 23;
        }

        if (Strings::contains($videoTitle, '22.11.2017')) {
            return 16;
        }

        if (Strings::match(
            $videoTitle,
            '#(17\. 4\. 2019|Čtyři hlavní příčiny dysfunkčních návyků v týmu – Michal Abaffy)#'
        )) {
            return 27;
        }

        if (Strings::contains($videoTitle, '15. 5. 2019')) {
            return 28;
        }

        throw new ShouldNotHappenException('Complete new rank for PeckaDesign meetup');
    }

    private function getMonthNameFromNumber(int $monthNumber): string
    {
        $numberToMonth = [1 => 'leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec'];

        return $numberToMonth[$monthNumber];
    }
}
