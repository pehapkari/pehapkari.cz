<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Command;

use Nette\Utils\Json;
use Pehapkari\Marketing\Social\FacebookIds;
use Pehapkari\Youtube\Yaml\YamlFileGenerator;
use Pehapkari\Youtube\YoutubeApi;
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

    public function __construct(
        YoutubeApi $youtubeApi,
        SymfonyStyle $symfonyStyle,
        YamlFileGenerator $yamlFileGenerator
    ) {
        $this->youtubeApi = $youtubeApi;
        $this->symfonyStyle = $symfonyStyle;
        $this->yamlFileGenerator = $yamlFileGenerator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->note('Importing videos from Pehapkari Youtube...');

        $data['parameters']['youtube_videos'] = $this->youtubeApi->getMeetupPlaylistsAndLivestreamPlaylist();

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
