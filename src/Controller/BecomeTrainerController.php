<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Pehapkari\Statistics\TrainingStatistics;
use Pehapkari\Training\Repository\TrainingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BecomeTrainerController extends AbstractController
{
    /**
     * @Route(path="zacni-skolit", name="become_trainer")
     */
    public function __invoke(TrainingStatistics $trainingStatistics, TrainingRepository $trainingRepository): Response
    {
        return $this->render('training/become_trainer.twig', [
            'total_training_term_count' => $trainingStatistics->getFinishedTrainingsCount(),
            'total_participant_count' => $trainingStatistics->getRegistrationCount(),
            'example_training' => $trainingRepository->getById(6),
        ]);
    }
}
