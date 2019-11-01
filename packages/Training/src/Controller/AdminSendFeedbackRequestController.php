<?php

declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Mailer\PehapkariMailer;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminSendFeedbackRequestController extends EasyAdminController
{

    /**
     * @Route(path="admin/send-training-term-feedback-form/{id}", name="send_traning_term_feedback_form")
     */
    public function __invoke(TrainingTerm $trainingTerm, PehapkariMailer $pehapkariMailer): Response
    {
        $pehapkariMailer->sendFeedbackFormToRegistrations($trainingTerm);

        return $this->redirectToRoute('training_term_organization', [
            'id' => $trainingTerm->getId()
        ]);
    }
}
