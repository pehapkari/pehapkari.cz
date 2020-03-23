<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Nette\Utils\Strings;
use Pehapkari\Youtube\Contract\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\MeetupNaming\MeetupNaming;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\ChannelList;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\PlaylistsProvider;

final class DigitalSolutionsYoutubeVideosProvider implements YoutubeVideosProviderInterface
{
    private YoutubeApi $youtubeApi;

    private VideosFactory $videosFactory;

    private PlaylistsProvider $playlistsProvider;

    private MeetupNaming $meetupNaming;

    public function __construct(
        YoutubeApi $youtubeApi,
        PlaylistsProvider $playlistsProvider,
        VideosFactory $videosFactory,
        MeetupNaming $meetupNaming
    ) {
        $this->videosFactory = $videosFactory;
        $this->playlistsProvider = $playlistsProvider;
        $this->youtubeApi = $youtubeApi;
        $this->meetupNaming = $meetupNaming;
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
        $playlistsData = $this->playlistsProvider->provideForChannel(ChannelList::DIGITAL_SOLUTIONS_CHANNEL_ID);

        $playlists = [];

        foreach ($playlistsData['items'] as $playlistItemData) {
            $videosInPlaylistData = $this->youtubeApi->getVideosByPlaylistId($playlistItemData['id']);

            $playlistTitle = $playlistItemData['snippet']['title'];
            $month = $this->resolvePlaylistMonth($playlistTitle);

            $playlists[] = [
                'title' => $this->createTitle($month, $playlistTitle),
                'videos' => $this->videosFactory->createVideos($videosInPlaylistData),
                'month' => $month,
            ];
        }

        return $playlists;
    }

    private function resolvePlaylistMonth(string $playlistTitle): ?string
    {
        if (Strings::startsWith($playlistTitle, '9. sraz')) {
            return '2019-06';
        }

        if (Strings::startsWith($playlistTitle, '8. sraz')) {
            return '2018-11';
        }

        if (Strings::startsWith($playlistTitle, '7. sraz')) {
            return '2018-06';
        }

        if (Strings::startsWith($playlistTitle, '6. sraz')) {
            return '2017-06';
        }

        return null;
    }

    private function createTitle(?string $month, string $playlistTitle): string
    {
        if ($month) {
            return $this->meetupNaming->createMeetupTitleWithMonth($playlistTitle, $month);
        }

        return $playlistTitle;
    }
}
