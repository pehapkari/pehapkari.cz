<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Registration\Repository\RegistrationRepository;
use Pehapkari\Repository\TrainerRepository;
use Pehapkari\Statistics\TrainingStatisticsProvider;
use Pehapkari\Training\Repository\TrainingFeedbackRepository;
use Pehapkari\Training\Repository\TrainingRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingsController extends AbstractController
{
    /**
     * @Route(path="/vzdelavej-se/", name="trainings")
     */
    public function __invoke(
        TrainingTermRepository $trainingTermRepository,
        TrainingRepository $trainingRepository,
        TrainingFeedbackRepository $trainingFeedbackRepository,
        RegistrationRepository $trainingRegistrationRepository,
        TrainerRepository $trainerRepository,
        TrainingStatisticsProvider $trainingStatisticsProvider
    ): Response {
        $averageRating = $trainingFeedbackRepository->getAverageRating();

        return $this->render('training/trainings.twig', [
            'upcoming_training_terms' => $trainingTermRepository->getUpcoming(),
            'inactive_trainings' => $trainingRepository->getInactiveTrainings(),

            'total_training_term_count' => $trainingStatisticsProvider->getFinishedTrainingsCount(),
            'total_participant_count' => $trainingStatisticsProvider->getRegistrationCount(),

            'feedbacks' => $trainingFeedbackRepository->getForMainPage(),

            'average_training_rating' => $averageRating,
            'average_training_rating_stars' => round($averageRating, 0),

            'trainer_count' => $trainerRepository->getCount(),
        ]);
    }
}
