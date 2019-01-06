<?php declare(strict_types=1);

namespace OpenTraining\Registration\Controller;

use OpenTraining\Registration\Invoicing\Invoicer;
use OpenTraining\Training\Entity\TrainingTerm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class InvoicingController extends AbstractController
{
    /**
     * @var Invoicer
     */
    private $invoicer;

    public function __construct(Invoicer $invoicer)
    {
        $this->invoicer = $invoicer;
    }

    /**
     * @Route(path="/admin/training-term/send-invoices/{id}", name="send_invoices")
     */
    public function sendInvoices(TrainingTerm $trainingTerm): Response
    {
        foreach ($trainingTerm->getRegistrations() as $registration) {
            if ($registration->isSentInvoice()) {
                continue;
            }

            $this->invoicer->sendInvoiceForRegistration($registration);
        }

        die;

//        return $this->render('training/become_trainer.twig', [
//            'places' => $this->placeRepository->fetchAll(),
//        ]);
    }
}
