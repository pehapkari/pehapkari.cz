<?php

declare(strict_types=1);

namespace Pehapkari\Controller;

use Nette\Utils\DateTime;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Repository\TrainingFeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingDetailController extends AbstractController
{
    /**
     * @Route(path="kurz/{slug}", name="training_detail")
     */
    public function __invoke(Training $training, TrainingFeedbackRepository $trainingFeedbackRepository): Response
    {
        return $this->render('training/training_detail.twig', [
            'training' => $training,
            'training_term' => $training->getNearestTerm(),
            'trainer' => $training->getTrainer(),
            'should_display_deadline' => $this->shouldDisplayDeadline($training),
        ]);
    }

    private function shouldDisplayDeadline(Training $training): bool
    {
        $nearestTerm = $training->getNearestTerm();
        if ($nearestTerm === null) {
            return false;
        }

        // show only on nearest X days
        $weekBackDateTime = (new DateTime())->modify(' - 14 days');

        return $nearestTerm->getDeadlineDateTime() < $weekBackDateTime;
    }
}
