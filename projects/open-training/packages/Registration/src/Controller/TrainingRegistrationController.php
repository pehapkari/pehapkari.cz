<?php declare(strict_types=1);

namespace OpenTraining\Registration\Controller;

use OpenTraining\AutowiredControllerTrait;
use OpenTraining\Registration\Entity\TrainingRegistration;
use OpenTraining\Registration\Form\TrainingRegistrationFormType;
use OpenTraining\Registration\Repository\TrainingRegistrationRepository;
use OpenTraining\Training\Entity\TrainingTerm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo registrační formulář - přidat * pro required položky
 */
final class TrainingRegistrationController
{
    use AutowiredControllerTrait;

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
            $this->trainingRegistrationRepository->save($trainingRegistration);

            $this->addFlash('success', 'Tvá registrace byla úspěšná!');

            return new RedirectResponse($this->generateUrl(
                'registration',
                [
                    'slug' => $trainingTerm->getSlug(),
                ]
            ));
        }

        return $this->render('registration/default.twig', [
            'training' => $trainingTerm->getTraining(),
            'trainingTerm' => $trainingTerm,
            'form' => $form->createView(),
        ]);
    }
}
