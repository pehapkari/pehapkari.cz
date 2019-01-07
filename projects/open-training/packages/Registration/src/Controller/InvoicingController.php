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
        if ($trainingTerm->getRegistrations() === []) {
            $this->addFlash('warning', 'Zatím tu nejsou žádné registrace.');
        } else {
            $hasNewInvoices = false;
            foreach ($trainingTerm->getRegistrations() as $registration) {
                if ($registration->hasInvoice()) {
                    continue;
                }

                $hasNewInvoices = true;
                $this->invoicer->sendInvoiceForRegistration($registration);

                $this->addFlash('success', 'Faktura pro ' . $registration->getTrainingName() . ' ' . $registration->getName() . ' byla vytvořena');
            }

            if ($hasNewInvoices === false) {
                $this->addFlash('success', 'Všechny registrace už svou fakturu mají');
            }
        }

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'TrainingTerm',
            'action' => 'list',
        ]);
    }
}
