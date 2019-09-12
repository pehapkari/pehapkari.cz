<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Command;

use DateTime;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Marketing\Entity\MarketingEvent;
use Pehapkari\Marketing\Repository\MarketingEventRepository;
use Pehapkari\Marketing\Social\TwitterPublisher;
use Pehapkari\Marketing\SocialPlatform;
use Pehapkari\Marketing\Utils\DateTimeUtils;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

final class PublishMarketingEventCommand extends Command
{
    /**
     * @var int
     */
    private const MINIMAL_HOUR_DIFFERENCE = 6;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var MarketingEventRepository
     */
    private $marketingEventRepository;

    /**
     * @var TwitterPublisher
     */
    private $twitterPublisher;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        MarketingEventRepository $marketingEventRepository,
        TwitterPublisher $twitterPublisher
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->marketingEventRepository = $marketingEventRepository;
        $this->twitterPublisher = $twitterPublisher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('Publish marketing events on social networks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nextPlannedMarketingEvent = $this->marketingEventRepository->getNextActiveEvent();
        if ($nextPlannedMarketingEvent === null) {
            $this->symfonyStyle->success('Nothing new to publish!');
            return ShellCode::SUCCESS;
        }

        if (! $this->canBeMarketingEventPublished($nextPlannedMarketingEvent)) {
            $this->symfonyStyle->warning('It is too soon to publish a new marketing event.');
            return ShellCode::SUCCESS;
        }

        $this->publishMarketingEvent($nextPlannedMarketingEvent);

        $this->symfonyStyle->success('OK');

        return ShellCode::SUCCESS;
    }

    /**
     * This should prevent spamming on social networks
     */
    private function canBeMarketingEventPublished(MarketingEvent $marketingEvent): bool
    {
        $latestPublishedEvent = $this->marketingEventRepository->getLatestPublishedEventByPlatform(
            $marketingEvent->getPlatform()
        );

        if ($latestPublishedEvent === null) {
            return true;
        }

        // at least X hours pause
        if ($latestPublishedEvent->getPublishedAt() === null) {
            return true;
        }

        $hourDiffs = DateTimeUtils::getHourDifferenceBetweenDateTimes(
            $marketingEvent->getPlannedAt(),
            $latestPublishedEvent->getPublishedAt()
        );

        return $hourDiffs > self::MINIMAL_HOUR_DIFFERENCE;
    }

    private function publishMarketingEvent(MarketingEvent $marketingEvent): void
    {
        if ($marketingEvent->getPlatform() === SocialPlatform::PLATFORM_TWITTER) {
            $this->twitterPublisher->publishMarketingEvent($marketingEvent);
        } else {
            throw new ShouldNotHappenException(sprintf(
                'Platform "%s" is not supported',
                $marketingEvent->getPlatform()
            ));
        }

        // save "when"
        $marketingEvent->setPublishedAt(new DateTime());
        $this->marketingEventRepository->save($marketingEvent);

        $trainingName = $marketingEvent->getTrainingTerm()->getTrainingName();

        $this->symfonyStyle->success(sprintf(
            'Event for "%s" and "%s" training was published.',
            lcfirst($marketingEvent->getPlatform()),
            $trainingName
        ));
    }
}
