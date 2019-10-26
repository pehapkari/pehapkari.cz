<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingRegistrationThankYouController extends AbstractController
{
    /**
     * @Route(path="/vitej-na-skoleni/{slug}/", name="registration_thank_you")
     */
    public function __invoke(TrainingTerm $trainingTerm): Response
    {
        return $this->render('registration/thank_you_for_registration.twig', [
            'trainingTerm' => $trainingTerm,
        ]);
    }
}
