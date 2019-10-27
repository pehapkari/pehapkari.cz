<?php

declare(strict_types=1);

namespace Pehapkari\Statistics;

use Pehapkari\Registration\Repository\RegistrationRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;

final class TrainingStatisticsProvider
{
    /**
     * @var RegistrationRepository
     */
    private $registrationRepository;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        RegistrationRepository $registrationRepository
    ) {
        $this->registrationRepository = $registrationRepository;
        $this->trainingTermRepository = $trainingTermRepository;
    }

    public function getFinishedTrainingsCount(): int
    {
        return $this->trainingTermRepository->getFinishedCount();
    }

    public function getRegistrationCount(): int
    {
        return $this->registrationRepository->getFinishedCount();
    }
}
