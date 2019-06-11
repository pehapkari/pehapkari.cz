<?php declare(strict_types=1);

namespace Pehapkari\Youtube\Command;

use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Pehapkari\Youtube\YoutubeApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class ImportVideosFromYoutubeCommand extends Command
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

    public function __construct(YoutubeApi $youtubeApi, SymfonyStyle $symfonyStyle)
    {
        $this->youtubeApi = $youtubeApi;
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $meetupPlaylistsAndLivestreamPlaylist = $this->youtubeApi->getMeetupPlaylistsAndLivestreamPlaylist();
        $data['parameters']['youtube_videos'] = $meetupPlaylistsAndLivestreamPlaylist;

        $yamlDump = Yaml::dump($data, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK);
        FileSystem::write(self::YOUTUBE_FILES_DATA, $this->createTimestampComment() . $yamlDump);

        $this->symfonyStyle->success('Videos were successfully imported from Pehapkari youtube');

        return ShellCode::SUCCESS;
    }

    private function createTimestampComment(): string
    {
        return sprintf(
            '# this file was generated on %s, do not edit it manually' . PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s')
        );
    }
}
