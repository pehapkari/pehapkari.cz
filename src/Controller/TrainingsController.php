<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Repository\TrainerRepository;
use Pehapkari\Statistics\TrainingStatistics;
use Pehapkari\Training\Repository\TrainingFeedbackRepository;
use Pehapkari\Training\Repository\TrainingRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingsController extends AbstractController
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    /**
     * @var TrainingRepository
     */
    private $trainingRepository;

    /**
     * @var TrainingFeedbackRepository
     */
    private $trainingFeedbackRepository;

    /**
     * @var TrainerRepository
     */
    private $trainerRepository;

    /**
     * @var TrainingStatistics
     */
    private $trainingStatistics;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        TrainingRepository $trainingRepository,
        TrainingFeedbackRepository $trainingFeedbackRepository,
        TrainerRepository $trainerRepository,
        TrainingStatistics $trainingStatistics
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->trainingRepository = $trainingRepository;
        $this->trainingFeedbackRepository = $trainingFeedbackRepository;
        $this->trainerRepository = $trainerRepository;
        $this->trainingStatistics = $trainingStatistics;
    }

    /**
     * @Route(path="vzdelavej-se", name="trainings")
     */
    public function __invoke(): Response
    {
        return $this->render('training/trainings.twig', [
            'upcoming_training_terms' => $this->trainingTermRepository->getUpcoming(),
            'inactive_trainings' => $this->trainingRepository->getInactiveTrainings(),

            'total_training_term_count' => $this->trainingStatistics->getFinishedTrainingsCount(),
            'total_participant_count' => $this->trainingStatistics->getRegistrationCount(),

            'feedbacks' => $this->trainingFeedbackRepository->getForMainPage(),

            'average_training_rating' => $this->trainingStatistics->getAverageTrainingRating(),
            'average_training_rating_stars' => $this->trainingStatistics->getAverageTrainingRatingStarsCount(),

            'trainer_count' => $this->trainerRepository->getCount(),
        ]);
    }
}
