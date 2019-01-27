<?php declare(strict_types=1);

namespace OpenTraining\Twig\Extension;

use OpenTraining\Registration\Repository\TrainingRegistrationRepository;
use OpenTraining\Training\Repository\TrainingTermRepository;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

/**
 * @see https://stackoverflow.com/a/27965813/1348344
 */
final class GlobalNumbersExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    /**
     * @var TrainingRegistrationRepository
     */
    private $trainingRegistrationRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        TrainingRegistrationRepository $trainingRegistrationRepository
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
    }

    /**
     * @return mixed[]
     */
    public function getGlobals(): array
    {
        return [
            'finishedTrainingTermCount' => $this->trainingTermRepository->getFinishedCount(),
            'finishedParticipantCount' => $this->trainingRegistrationRepository->getFinishedCount(),
        ];
    }
}
