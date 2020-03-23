<?php

declare(strict_types=1);

namespace Pehapkari\Youtube;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use Nette\Utils\Json;
use Pehapkari\Youtube\Exception\YoutubeApiException;

final class YoutubeApi
{
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

    private string $youtubeApiKey;

    private Client $client;

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
    public function getVideosByPlaylistId(string $id): array
    {
        $url = sprintf(self::ENDPOINT_VIDEOS_BY_PLAYLIST, $id);

        return $this->getData($url);
    }

    /**
     * @return mixed[]
     */
    public function getPlaylistsByChannel(string $channelId): array
    {
        return $this->getData(sprintf(self::ENPOINT_PLAYLISTS_BY_CHANNEL, $channelId));
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
