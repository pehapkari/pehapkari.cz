<?php declare(strict_types=1);

namespace Pehapkari\Registration\Command;

use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Console\ShellCode;

/**
 * @cron
 */
final class DeleteEmptyTrainingTermsCommand extends Command
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(TrainingTermRepository $trainingTermRepository, SymfonyStyle $symfonyStyle)
    {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->symfonyStyle = $symfonyStyle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->symfonyStyle->warning('Deleting old training terms with 0 participants');

        $finishedAndEmptyTrainingTerms = $this->trainingTermRepository->fetchFinishedAndEmpty();
        if (count($finishedAndEmptyTrainingTerms) === 0) {
            $this->symfonyStyle->success('Nothing to delete');

            return ShellCode::SUCCESS;
        }

        foreach ($finishedAndEmptyTrainingTerms as $trainingTerm) {
            $this->symfonyStyle->note(sprintf(
                $trainingTerm . ' is deleted for %d participant(s)',
                $trainingTerm->getParticipantCount()
            ));

            $this->trainingTermRepository->delete($trainingTerm);
        }

        $this->symfonyStyle->success(
            sprintf('Deleted "%d" old and empty training terms', count($finishedAndEmptyTrainingTerms))
        );

        return ShellCode::SUCCESS;
    }
}
