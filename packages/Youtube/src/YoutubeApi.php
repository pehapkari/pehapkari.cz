<?php declare(strict_types=1);

namespace Pehapkari\Youtube;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Pehapkari\Youtube\Exception\YoutubeApiException;

final class YoutubeApi
{
    /**
     * @var string
     */
    public const KIND_LIVESTREAM = 'livestream';

    /**
     * @var string
     */
    public const KIND_PHP_PRAGUE_CONFERENCE = 'php_prague_conference';

    /**
     * @var string
     */
    public const KIND_MEETUP = 'meetup';

    /**
     * @var string
     */
    private const PEHAPKARI_CHANNEL_ID = 'UCTBgI1P8xIn2pp2BBHbv5mg';

    /**
     * @var string
     * @see https://developers.google.com/youtube/v3/docs/playlistItems/list
     */
    private const ENDPOINT_VIDEOS_BY_PLAYLIST = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=%s&maxResults=50';

    /**
     * 50 is allowed maximum
     * @var string
     */
    private const ENPOINT_PLAYLISTS_BY_CHANNEL = 'https://www.googleapis.com/youtube/v3/playlists?part=snippet,contentDetails&channelId=%s&maxResults=50';

    /**
     * @var string
     */
    private $youtubeApiKey;

    /**
     * @var Client
     */
    private $client;

    public function __construct(string $youtubeApiKey)
    {
        $this->client = new Client([
            'verify' => CaBundle::getSystemCaRootBundlePath(),
        ]);

        $this->youtubeApiKey = $youtubeApiKey;
    }

    /**
     * @return mixed[]
     */
    public function getMeetupPlaylistsAndLivestreamPlaylist(): array
    {
        $playlists = [];

        $playlistsData = $this->getPlaylistsByChannel(self::PEHAPKARI_CHANNEL_ID);

        foreach ($playlistsData['items'] as $item) {
            $videosInPlaylistData = $this->getVideosByPlaylist($item);

            $kind = $this->resolveVideoKind($item['snippet']['title']);

            $videos = $this->createVideos($videosInPlaylistData, $kind);
            $playlist = $this->createPlaylist($item, $videos);

            $playlist['month'] = $this->resolvePlaylistMonth($item['snippet']['title']);

            if ($kind === self::KIND_LIVESTREAM) {
                $playlists['livestream_playlist'] = $playlist;
            } elseif ($kind === self::KIND_PHP_PRAGUE_CONFERENCE) {
                $playlists['php_prague_playlist'] = $playlist;
            } else {
                $playlists['meetup_playlists'][] = $playlist;
            }
        }

        // sort playlists by month, newest up
        if (isset($playlists['meetup_playlists'])) {
            usort($playlists['meetup_playlists'], function (array $firstPlaylist, array $secondPlaylist): int {
                return $secondPlaylist['month'] <=> $firstPlaylist['month'];
            });
        }

        return $playlists;
    }

    /**
     * @return mixed[]
     */
    private function getPlaylistsByChannel(string $channelId): array
    {
        return $this->getData(sprintf(self::ENPOINT_PLAYLISTS_BY_CHANNEL, $channelId));
    }

    /**
     * @param mixed[] $item
     * @return mixed[]
     */
    private function getVideosByPlaylist(array $item): array
    {
        $url = sprintf(self::ENDPOINT_VIDEOS_BY_PLAYLIST, $item['id']);

        return $this->getData($url);
    }

    private function resolveVideoKind(string $playlistTitle): string
    {
        if ($playlistTitle === 'Twitch Livestream') {
            return self::KIND_LIVESTREAM;
        }

        if (Strings::match($playlistTitle, '#PHP( )?Prague#i')) {
            return self::KIND_PHP_PRAGUE_CONFERENCE;
        }

        return self::KIND_MEETUP;
    }

    /**
     * @param mixed[] $videoItems
     * @return mixed[]
     */
    private function createVideos(array $videoItems, string $kind): array
    {
        $videos = [];

        foreach ($videoItems['items'] as $videoItem) {
            // skip private videos
            if ($videoItem['snippet']['title'] === 'Private video') {
                continue;
            }

            $title = $videoItem['snippet']['title'];
            $match = Strings::match($title, '#(?<name>.*?) - (?<title>.*?)$#');

            $video = [
                'title' => $match['title'] ?? $title,
                'speaker' => $match['name'] ?? '',
                'description' => $videoItem['snippet']['description'],
                'video_id' => $videoItem['snippet']['resourceId']['videoId'],
                'slug' => Strings::webalize($title),
                'kind' => $kind,
                'published_at' => DateTime::from($videoItem['snippet']['publishedAt']),
            ];

            $match = Strings::match($video['description'], '#(Slajdy|Slidy)(.*?): (?<slides>[\w:\/\.\-\_]+)#s');
            $video['slides'] = $match['slides'] ?? '';

            $thumbnails = $videoItem['snippet']['thumbnails'] ?? null;
            if (isset($thumbnails['standard'])) {
                $video['thumbnail'] = $thumbnails['standard']['url'];
            } elseif (isset($thumbnails['high'])) {
                $video['thumbnail'] = $thumbnails['high']['url'];
            }

            $videos[] = $video;
        }

        return $videos;
    }

    /**
     * @param mixed[] $item
     * @param mixed[] $videos
     * @return mixed[]
     */
    private function createPlaylist(array $item, array $videos): array
    {
        return [
            'title' => $item['snippet']['title'],
            'published_at' => DateTime::from($item['snippet']['publishedAt']),
            'videos' => $videos,
        ];
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

    /**
     * @return mixed[]
     */
    private function getData(string $url): array
    {
        $link = $url . '&key=' . $this->youtubeApiKey;

        $response = $this->client->request('GET', $link);
        if ($response->getStatusCode() !== 200) {
            throw new YoutubeApiException(sprintf('Unable load data for "%s"', $url));
        }

        return Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);
    }
}
