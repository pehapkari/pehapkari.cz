<?php

declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Mailer\PehapkariMailer;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminSendFeedbackRequestController extends EasyAdminController
{
    private PehapkariMailer $pehapkariMailer;

    private TrainingTermRepository $trainingTermRepository;

    public function __construct(PehapkariMailer $pehapkariMailer, TrainingTermRepository $trainingTermRepository)
    {
        $this->pehapkariMailer = $pehapkariMailer;
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @Route(path="admin/send-training-term-feedback-form/{id}", name="send_traning_term_feedback_form")
     */
    public function __invoke(TrainingTerm $trainingTerm): Response
    {
        if ($trainingTerm->areFeedbackEmailsSent() === false) {
            $this->sendFeedbackEmails($trainingTerm);
        } else {
            $this->addFlash('warning', 'Email s feedbacky už byl poslán');
        }

        return $this->redirectToRoute('training_term_organization', [
            'id' => $trainingTerm->getId(),
        ]);
    }

    private function sendFeedbackEmails(TrainingTerm $trainingTerm): void
    {
        $this->pehapkariMailer->sendFeedbackFormToRegistrations($trainingTerm);
        $trainingTerm->setAreFeedbackEmailsSent(true);

        $this->trainingTermRepository->save($trainingTerm);

        foreach ($trainingTerm->getRegistrations() as $registration) {
            $this->addFlash('success', sprintf('Email s feedbackem poslán na "%s"', $registration->getEmail()));
        }
    }
}
