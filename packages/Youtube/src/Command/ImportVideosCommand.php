<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Command;

use Nette\Utils\Json;
use Pehapkari\Marketing\Social\FacebookIds;
use Pehapkari\Youtube\Contract\YoutubeVideosProvider\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\Yaml\YamlFileGenerator;
use Pehapkari\Youtube\YoutubeApi;
use Pehapkari\Youtube\YoutubeVideosProvider\PeckaDesignYoutubeVideosProvider;
use Pehapkari\Youtube\YoutubeVideosProvider\PehapkariMeetupsYoutubeVideosProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ImportVideosCommand extends Command
{
    /**
     * @var string
     */
    private const YOUTUBE_FILES_DATA = __DIR__ . '/../../../../config/_data/youtube_videos.yaml';

    /**
     * @var YoutubeApi
     */
    private $youtubeApi;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var YamlFileGenerator
     */
    private $yamlFileGenerator;
    /**
     * @var PeckaDesignYoutubeVideosProvider
     */
    private $peckaDesignYoutubeVideosProvider;
    /**
     * @var PehapkariMeetupsYoutubeVideosProvider
     */
    private $pehapkariYoutubeVideosProvider;
    /**
     * @var array|YoutubeVideosProviderInterface[]
     */
    private $youtubeVideosProviders;

    /**
     * @param YoutubeVideosProviderInterface[] $youtubeVideosProviders
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        YamlFileGenerator $yamlFileGenerator,
        PeckaDesignYoutubeVideosProvider $peckaDesignYoutubeVideosProvider,
        PehapkariMeetupsYoutubeVideosProvider $pehapkariYoutubeVideosProvider,
        array $youtubeVideosProviders
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->yamlFileGenerator = $yamlFileGenerator;
        $this->peckaDesignYoutubeVideosProvider = $peckaDesignYoutubeVideosProvider;

        parent::__construct();
        $this->pehapkariYoutubeVideosProvider = $pehapkariYoutubeVideosProvider;
        $this->youtubeVideosProviders = $youtubeVideosProviders;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $youtubeVideosData = [];
        foreach ($this->youtubeVideosProviders as $youtubeVideosProvider) {
            $name = $youtubeVideosProvider->getName();
            $this->symfonyStyle->note(sprintf('Importing playlists for "%s"', $name));

            $playlists = $youtubeVideosProvider->providePlaylists();
            $youtubeVideosData[$name] = array_merge($playlists, $youtubeVideosData[$name] ?? []);
        }

        // sort meetup playlists by month, the newest first
        if (isset($youtubeVideosData['meetups'])) {
            usort($youtubeVideosData['meetups'], function (array $firstPlaylist, array $secondPlaylist): int {
                return $secondPlaylist['month'] <=> $firstPlaylist['month'];
            });
        }

        $data['parameters']['youtube_videos'] = $youtubeVideosData;

        $this->yamlFileGenerator->generate($data, self::YOUTUBE_FILES_DATA);
        $this->symfonyStyle->success('Videos were successfully imported!');

        return ShellCode::SUCCESS;
    }

    /**
     * @todo push application so it gets accepted by FB
     */
    private function prepareFacebookVideos(): void
    {
        return;

        // https://developers.facebook.com/docs/graph-api/reference/page/video_lists/
        $endPoint = FacebookIds::PEHAPKARI_PAGE_ID . '/video_lists';
        $response = $this->facebook->get($endPoint);

        $data = Json::decode($response->getBody(), Json::FORCE_ARRAY)['data'];

        foreach ($data as $item) {
            // https://developers.facebook.com/docs/graph-api/reference/video-list/
            $endPoint = $item['id'] . '?fields=videos';
            $response = $this->facebook->get($endPoint);

            $data = Json::decode($response->getBody(), Json::FORCE_ARRAY);
        }
    }
}
