<?php

declare(strict_types=1);

namespace Pehapkari\Statistics;

use Pehapkari\Registration\Repository\RegistrationRepository;
use Pehapkari\Training\Repository\TrainingFeedbackRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;

final class TrainingStatistics
{
    /**
     * @var RegistrationRepository
     */
    private $registrationRepository;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    /**
     * @var TrainingFeedbackRepository
     */
    private $trainingFeedbackRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        RegistrationRepository $registrationRepository,
        TrainingFeedbackRepository $trainingFeedbackRepository
    ) {
        $this->registrationRepository = $registrationRepository;
        $this->trainingTermRepository = $trainingTermRepository;
        $this->trainingFeedbackRepository = $trainingFeedbackRepository;
    }

    public function getFinishedTrainingsCount(): int
    {
        return $this->trainingTermRepository->getFinishedCount();
    }

    public function getRegistrationCount(): int
    {
        return $this->registrationRepository->getFinishedCount();
    }

    public function getAverageTrainingRating(): float
    {
        return $this->trainingFeedbackRepository->getAverageRating();
    }

    public function getAverageTrainingRatingStarsCount(): int
    {
        return (int) round($this->getAverageTrainingRating(), 0);
    }
}
