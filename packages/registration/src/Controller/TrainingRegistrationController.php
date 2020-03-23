<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingRegistrationController extends AbstractController
{
    private TrainingTermRepository $trainingTermRepository;

    public function __construct(TrainingTermRepository $trainingTermRepository)
    {
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @Route(path="prehled-registraci", name="registration-overview")
     */
    public function __invoke(): Response
    {
        return $this->render('registration/overview.twig', [
            'upcoming_training_terms' => $this->trainingTermRepository->getUpcoming(),
        ]);
    }
}
