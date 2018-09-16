<?php declare(strict_types=1);

namespace OpenTraining\Registration\Controller;

use OpenTraining\Registration\Entity\TrainingRegistration;
use OpenTraining\Registration\Form\TrainingRegistrationFormType;
use OpenTraining\Registration\Repository\TrainingRegistrationRepository;
use OpenTraining\Training\Entity\Training;
use OpenTraining\Training\Entity\TrainingTerm;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @todo registrační formulář - přidat * pro required položky
 */
final class TrainingRegistrationController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var TrainingRegistrationRepository
     */
    private $trainingRegistrationRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        EngineInterface $templatingEngine,
        FormFactoryInterface $formFactory,
        TrainingRegistrationRepository $trainingRegistrationRepository,
        FlashBagInterface $flashBag,
        RouterInterface $router
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->formFactory = $formFactory;
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
        $this->flashBag = $flashBag;
        $this->router = $router;
    }

    /**
     * @Route(path="/registration/{slug}", name="registration", methods={"GET", "POST"})
     * @todo název-školení/datum
     *
     * @see https://github.com/symfony/demo/blob/master/src/Controller/Admin/BlogController.php
     */
    public function default(Request $request, TrainingTerm $trainingTerm): Response
    {
        $form = $this->formFactory->create(TrainingRegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TrainingRegistration $trainingRegistration */
            $trainingRegistration = $form->getData();
            $this->trainingRegistrationRepository->save($trainingRegistration);

            // @todo translate
            $this->flashBag->add('success', 'training.registration_successful');

            return new RedirectResponse($this->router->generate(
                'registration',
                ['trainingTerm' => $trainingTerm->getId()]
            ));
        }

        return $this->templatingEngine->renderResponse('registration/default.twig', [
            'training' => $trainingTerm->getTraining(),
            'trainingTerm' => $trainingTerm,
            'form' => $form->createView(),
        ]);
    }
}
