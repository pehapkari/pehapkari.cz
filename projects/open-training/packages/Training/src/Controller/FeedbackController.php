<?php declare(strict_types=1);

namespace OpenTraining\Training\Controller;

use OpenTraining\Training\Entity\TrainingFeedback;
use OpenTraining\Training\Form\FeedbackFormType;
use OpenTraining\Training\Repository\TrainingFeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @todo copy feedback form
 * @todo use bit.ly shortcut
 *
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
     * @Route(path="/jak-se-ti-libilo/", name="open_feedbacks")
     */
    public function openFeedbacks(Request $request): Response
    {
        $form = $this->createForm(FeedbackFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TrainingFeedback $trainingFeedback */
            $trainingFeedback = $form->getData();
            $this->trainingFeedbackRepository->save($trainingFeedback);

            return $this->redirectToRoute('thank_you_for_freedback');
        }

        return $this->render('feedback/open_feedbacks.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/diky-za-feedback/", name="thank_you_for_freedback")
     */
    public function thankYouForFeedback(): Response
    {
        return $this->render('feedback/thank_you_for_freedback.twig');
    }
}
