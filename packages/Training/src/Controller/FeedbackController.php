<?php

declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use Pehapkari\Training\Entity\TrainingFeedback;
use Pehapkari\Training\Form\FeedbackFormType;
use Pehapkari\Training\Repository\TrainingFeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @see TrainingFeedback
 */
final class FeedbackController extends AbstractController
{
    /**
     * @var TrainingFeedbackRepository
     */
    private $trainingFeedbackRepository;

    public function __construct(TrainingFeedbackRepository $trainingFeedbackRepository)
    {
        $this->trainingFeedbackRepository = $trainingFeedbackRepository;
    }

    /**
     * @Route(path="feedback")
     * @Route(path="feedbacks")
     * @Route(path="jak-se-ti-libilo")
     */
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(FeedbackFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TrainingFeedback $trainingFeedback */
            $trainingFeedback = $form->getData();
            $this->trainingFeedbackRepository->save($trainingFeedback);

            return $this->redirectToRoute('thank_you_for_feedback');
        }

        return $this->render('feedback/open_feedbacks.twig', [
            'form' => $form->createView(),
        ]);
    }
}
