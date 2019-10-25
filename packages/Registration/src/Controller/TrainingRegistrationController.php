<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingRegistrationController extends AbstractController
{
    /**
     * @Route(path="/prehled-registraci/", name="registration-overview", methods={"GET"})
     */
    public function run(TrainingTermRepository $trainingTermRepository): Response
    {
        return $this->render('registration/overview.twig', [
            'upcoming_training_terms' => $trainingTermRepository->getUpcoming(),
        ]);
    }
}
