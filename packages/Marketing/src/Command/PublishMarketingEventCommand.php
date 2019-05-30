<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Command;

use Pehapkari\Marketing\Repository\MarketingEventRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class PublishMarketingEventCommand extends Command
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var MarketingEventRepository
     */
    private $marketingEventRepository;

    public function __construct(SymfonyStyle $symfonyStyle, MarketingEventRepository $marketingEventRepository)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->marketingEventRepository = $marketingEventRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Publish marketing events on social networks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $activeMarketingEvents = $this->marketingEventRepository->findActive();

        // @todo publish those active
        // twitter publisher - use from Statie :)
        // facebook publisher
        dump($activeMarketingEvents);
        die;

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }
}
