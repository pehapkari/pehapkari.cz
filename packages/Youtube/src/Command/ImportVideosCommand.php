<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Command;

use Nette\Utils\Json;
use Pehapkari\Marketing\Social\FacebookIds;
use Pehapkari\Youtube\Contract\YoutubeVideosProvider\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\Sorter\ArrayByDateTimeSorter;
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
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var YamlFileGenerator
     */
    private $yamlFileGenerator;

    /**
     * @var YoutubeVideosProviderInterface[]
     */
    private $youtubeVideosProviders = [];
    /**
     * @var ArrayByDateTimeSorter
     */
    private $arrayByDateTimeSorter;

    /**
     * @param YoutubeVideosProviderInterface[] $youtubeVideosProviders
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        YamlFileGenerator $yamlFileGenerator,
        ArrayByDateTimeSorter $arrayByDateTimeSorter,
        array $youtubeVideosProviders
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->yamlFileGenerator = $yamlFileGenerator;
        $this->youtubeVideosProviders = $youtubeVideosProviders;
        $this->arrayByDateTimeSorter = $arrayByDateTimeSorter;

        parent::__construct();
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
            $youtubeVideosData['meetups'] = $this->arrayByDateTimeSorter->sortByKey($youtubeVideosData['meetups'], 'month');
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
