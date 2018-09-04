<?php declare(strict_types=1);

namespace OpenTraining\Registration\Controller;

use App\Entity\Training;
use OpenTraining\Registration\Entity\TrainingRegistration;
use OpenTraining\Registration\Form\TrainingRegistrationFormType;
use OpenTraining\Registration\Repository\TrainingRegistrationRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

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
     * @Route(path="/register-to-training/{training}", name="register-to-training", methods={"GET", "POST"})
     *
     * @see https://github.com/symfony/demo/blob/master/src/Controller/Admin/BlogController.php
     */
    public function default(Request $request, Training $training): Response
    {
        $form = $this->formFactory->create(TrainingRegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TrainingRegistration $trainingRegistration */
            $trainingRegistration = $form->getData();
            $this->trainingRegistrationRepository->save($trainingRegistration);

            // @todo translate
            $this->flashBag->add('succes', 'training.registration_successful');

            return new RedirectResponse($this->router->generate(
                'register-to-training',
                ['training' => $training->getId()]
            ));
        }

        return $this->templatingEngine->renderResponse('registration/default.twig', [
            'training' => $training,
            'form' => $form->createView(),
        ]);
    }
}
