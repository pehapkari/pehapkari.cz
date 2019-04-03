<?php declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Form\TrainingRegistrationFormType;
use Pehapkari\Registration\Repository\TrainingRegistrationRepository;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingRegistrationController extends AbstractController
{
    /**
     * @var TrainingRegistrationRepository
     */
    private $trainingRegistrationRepository;

    public function __construct(TrainingRegistrationRepository $trainingRegistrationRepository)
    {
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
    }

    /**
     * @Route(path="/registration/{slug}/", name="registration", methods={"GET", "POST"})
     *
     * @see https://github.com/symfony/demo/blob/master/src/Controller/Admin/BlogController.php
     */
    public function default(Request $request, TrainingTerm $trainingTerm): Response
    {
        $form = $this->createForm(TrainingRegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TrainingRegistration $trainingRegistration */
            $trainingRegistration = $form->getData();

            $trainingRegistration->setPrice($trainingTerm->getPrice());

            $this->trainingRegistrationRepository->save($trainingRegistration);

            $this->addFlash('success', 'Tvá registrace byla úspěšná!');

            return $this->redirectToRoute('registration_thank_you', [
                'slug' => $trainingTerm->getSlug(),
            ]);
        }

        return $this->render('registration/default.twig', [
            'training' => $trainingTerm->getTraining(),
            'trainingTerm' => $trainingTerm,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/vitej-na-skoleni/{slug}/", name="registration_thank_you")
     */
    public function thankYou(TrainingTerm $trainingTerm): Response
    {
        return $this->render('registration/thank_you.twig', [
            'trainingTerm' => $trainingTerm,
        ]);
    }
}
