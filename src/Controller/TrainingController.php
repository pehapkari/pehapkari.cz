<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Registration\Repository\TrainingRegistrationRepository;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Repository\TrainerRepository;
use Pehapkari\Training\Repository\TrainingFeedbackRepository;
use Pehapkari\Training\Repository\TrainingRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingController extends AbstractController
{
    /**
     * @var TrainingRepository
     */
    private $trainingRepository;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    /**
     * @var TrainingFeedbackRepository
     */
    private $trainingFeedbackRepository;

    /**
     * @var TrainingRegistrationRepository
     */
    private $trainingRegistrationRepository;

    /**
     * @var TrainerRepository
     */
    private $trainerRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        TrainingRepository $trainingRepository,
        TrainingFeedbackRepository $trainingFeedbackRepository,
        TrainingRegistrationRepository $trainingRegistrationRepository,
        TrainerRepository $trainerRepository
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->trainingRepository = $trainingRepository;
        $this->trainingFeedbackRepository = $trainingFeedbackRepository;
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
        $this->trainerRepository = $trainerRepository;
    }

    /**
     * @Route(path="/vzdelavej-se/", name="trainings")
     */
    public function trainings(): Response
    {
        $averageRating = $this->trainingFeedbackRepository->getAverageRating();

        return $this->render('training/trainings.twig', [
            'upcoming_training_terms' => $this->trainingTermRepository->getUpcoming(),
            'inactive_trainings' => $this->trainingRepository->getInactiveTrainings(),

            'total_training_term_count' => $this->trainingTermRepository->getFinishedCount(),
            'total_participant_count' => $this->trainingRegistrationRepository->getFinishedCount(),

            'feedbacks' => $this->trainingFeedbackRepository->getForMainPage(),

            'average_training_rating' => $averageRating,
            'average_training_rating_stars' => round($averageRating, 0),

            'past_terms' => $this->trainingTermRepository->getFinishedAndEmpty(),
            'past_terms_count' => count($this->trainingTermRepository->getFinishedAndEmpty()),

            'trainer_count' => count($this->trainerRepository->fetchAll()),
        ]);
    }

    /**
     * @Route(path="/kurz/{slug}", name="training_detail")
     */
    public function detail(Training $training): Response
    {
        return $this->render('training/training_detail.twig', [
            'training' => $training,
            'training_term' => $training->getNearestTerm(),
            'trainer' => $training->getTrainer(),
        ]);
    }
}
