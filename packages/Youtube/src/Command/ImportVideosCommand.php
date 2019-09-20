<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Command;

use Pehapkari\Youtube\Contract\YoutubeVideosProviderInterface;
use Pehapkari\Youtube\Sorter\ArrayByDateTimeSorter;
use Pehapkari\Youtube\Yaml\YamlFileGenerator;
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
     * @var YoutubeVideosProviderInterface[]
     */
    private $youtubeVideosProviders = [];

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var YamlFileGenerator
     */
    private $yamlFileGenerator;

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
        $this->symfonyStyle->section('Importing videos from Youtube');
        $data['parameters']['youtube_videos'] = $this->importYoutubeVideosData();

        $this->yamlFileGenerator->generate($data, self::YOUTUBE_FILES_DATA);
        $this->symfonyStyle->success('Videos were successfully imported!');

        return ShellCode::SUCCESS;
    }

    /**
     * @return mixed[]
     */
    private function importYoutubeVideosData(): array
    {
        $youtubeVideosData = [];
        foreach ($this->youtubeVideosProviders as $youtubeVideosProvider) {
            $name = $youtubeVideosProvider->getName();
            $this->symfonyStyle->note(sprintf('Importing Youtube videos for "%s"', $name));

            $playlists = $youtubeVideosProvider->providePlaylists();
            $youtubeVideosData[$name] = array_merge($playlists, $youtubeVideosData[$name] ?? []);
        }

        return $this->sortMeetupPlaylistsByMonthFromRecentToOld($youtubeVideosData);
    }

    /**
     * @param mixed[] $youtubeVideosData
     * @return mixed[]
     */
    private function sortMeetupPlaylistsByMonthFromRecentToOld(array $youtubeVideosData): array
    {
        if (! isset($youtubeVideosData['meetups'])) {
            return $youtubeVideosData;
        }

        $youtubeVideosData['meetups'] = $this->arrayByDateTimeSorter->sortByKey(
            $youtubeVideosData['meetups'],
            'month'
        );

        return $youtubeVideosData;
    }
}
