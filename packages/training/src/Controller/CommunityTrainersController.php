<?php

declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use Pehapkari\Training\Repository\TrainerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CommunityTrainersController extends AbstractController
{
    private TrainerRepository $trainerRepository;

    public function __construct(TrainerRepository $trainerRepository)
    {
        $this->trainerRepository = $trainerRepository;
    }

    /**
     * @Route(path="community-trainers", name="community_trainers")
     */
    public function __invoke(): Response
    {
        $trainers = $this->trainerRepository->fetchAllSortedByTrainingTermCount();

        $trainingTermCount = 0;
        foreach ($trainers as $trainer) {
            $trainingTermCount += $trainer->getTrainingTermCount();
        }

        return $this->render('training/community_trainers.twig', [
            'trainers' => $trainers,
            'training_term_count' => $trainingTermCount,
        ]);
    }
}
