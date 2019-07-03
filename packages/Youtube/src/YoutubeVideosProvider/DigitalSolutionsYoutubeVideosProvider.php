<?php declare(strict_types=1);

namespace Pehapkari\Youtube\YoutubeVideosProvider;

use Nette\Utils\Strings;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Youtube\Contract\YoutubeVideosProvider\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\DataTransformer\VideosFactory;
use Pehapkari\Youtube\MeetupNaming\MeetupNaming;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\Channel\DigitalSolutionsPlaylistsProvider;

final class DigitalSolutionsYoutubeVideosProvider implements YoutubeVideosProviderInterface
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
     * @var DigitalSolutionsPlaylistsProvider
     */
    private $digitalSolutionsPlaylistsProvider;

    /**
     * @var MeetupNaming
     */
    private $meetupNaming;

    public function __construct(
        YoutubeApi $youtubeApi,
        DigitalSolutionsPlaylistsProvider $digitalSolutionsPlaylistsProvider,
        VideosFactory $videosFactory,
        MeetupNaming $meetupNaming
    ) {
        $this->videosFactory = $videosFactory;
        $this->digitalSolutionsPlaylistsProvider = $digitalSolutionsPlaylistsProvider;
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
        $playlistsData = $this->digitalSolutionsPlaylistsProvider->provide();

        $playlists = [];

        foreach ($playlistsData['items'] as $playlistItemData) {
            $videosInPlaylistData = $this->youtubeApi->getVideosByPlaylistId($playlistItemData['id']);

            $playlistTitle = $playlistItemData['snippet']['title'];

            $month = $this->resolvePlaylistMonth($playlistTitle);

            $playlists[] = [
                'title' => $this->meetupNaming->createMeetupTitleWithMonth($playlistTitle, $month),
                'videos' => $this->videosFactory->createVideos($videosInPlaylistData),
                'month' => $month,
            ];
        }

        return $playlists;
    }

    private function resolvePlaylistMonth(string $playlistTitle): string
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

        throw new ShouldNotHappenException();
    }
}
