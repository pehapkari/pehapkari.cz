<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Command;

use Pehapkari\Marketing\Entity\MarketingEvent;
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
//        $activeMarketingEvents = $this->marketingEventRepository->findActive();

        $nextPlannedMarketingEvent = $this->marketingEventRepository->getNextActiveEvent();
        if ($nextPlannedMarketingEvent === null) {
            $this->symfonyStyle->success('Nothing new to publish!');
            return ShellCode::SUCCESS;
        }


        if (! $this->canBeMarketingEventPublished($nextPlannedMarketingEvent)) {
            $this->symfonyStyle->warning('It is too soon to publish a new marketing event.');
            return ShellCode::SUCCESS;
        }

        // @todo publish those active
        // twitter publisher - use from Statie :)
        // facebook publisher
        die;

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }

    /**
     * This should prevent spamming on social networks
     */
    private function canBeMarketingEventPublished(MarketingEvent $marketingEvent)
    {
        $latestPublishedEvent = $this->marketingEventRepository->getLatestPublishedEventByPlatform($marketingEvent->getPlatform());
        if ($latestPublishedEvent === null) {
            return true;
        }

        // at least X hours pause
        $latestPublishedEvent->getPublishedAt();
        dump($marketingEvent->getPlannedAt());

        die;
    }
}
