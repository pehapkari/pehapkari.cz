<?php

declare(strict_types=1);

namespace Pehapkari\Youtube\Command;

use Pehapkari\Youtube\Contract\YoutubeVideosProviderInterface;
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

    private YamlFileGenerator $yamlFileGenerator;

    private SymfonyStyle $symfonyStyle;

    /**
     * @var YoutubeVideosProviderInterface[]
     */
    private array $youtubeVideosProviders = [];

    /**
     * @param YoutubeVideosProviderInterface[] $youtubeVideosProviders
     */
    public function __construct(
        SymfonyStyle $symfonyStyle,
        YamlFileGenerator $yamlFileGenerator,
        array $youtubeVideosProviders
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->yamlFileGenerator = $yamlFileGenerator;
        $this->youtubeVideosProviders = $youtubeVideosProviders;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Imports videos from Youtube Pehapkari channel');
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
            $youtubeVideosData[$name] = [...$playlists, ...$youtubeVideosData[$name] ?? []];
        }

        return $youtubeVideosData;
    }
}
