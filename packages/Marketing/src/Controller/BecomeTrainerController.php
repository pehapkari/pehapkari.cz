<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Controller;

use Pehapkari\Training\Repository\TrainingRepository;
use Pehapkari\Training\Statistics\TrainingStatistics;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class BecomeTrainerController extends AbstractController
{
    /**
     * @var TrainingStatistics
     */
    private $trainingStatistics;

    /**
     * @var TrainingRepository
     */
    private $trainingRepository;

    public function __construct(TrainingStatistics $trainingStatistics, TrainingRepository $trainingRepository)
    {
        $this->trainingStatistics = $trainingStatistics;
        $this->trainingRepository = $trainingRepository;
    }

    /**
     * @Route(path="zacni-skolit", name="become_trainer")
     */
    public function __invoke(): Response
    {
        return $this->render('training/become_trainer.twig', [
            'total_training_term_count' => $this->trainingStatistics->getFinishedTrainingsCount(),
            'total_participant_count' => $this->trainingStatistics->getRegistrationCount(),
            'example_training' => $this->trainingRepository->getById(6),
        ]);
    }
}
