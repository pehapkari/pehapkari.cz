<?php declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use Pehapkari\Registration\Invoicing\Invoicer;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @todo run via cron/api
     * @Route(path="/invoices/sync-paid-invoices", name="sync_paid_invoices")
     */
    public function syncPaidInvoices(): Response
    {
        $syncedRegistrationCount = $this->invoicer->syncPaidInvoices();

        return new JsonResponse([
            'synced_registration_count' => $syncedRegistrationCount,
        ]);
    }

    /**
     * @Route(path="/admin/training-term/send-invoices/{id}", name="send_invoices")
     */
    public function sendInvoices(TrainingTerm $trainingTerm): Response
    {
        if ($trainingTerm->getRegistrationCount() === 0) {
            $this->addFlash('warning', 'Zatím tu nejsou žádné registrace.');
        } else {
            $hasNewInvoices = false;
            foreach ($trainingTerm->getRegistrations() as $registration) {
                if ($registration->hasInvoice()) {
                    continue;
                }

                $hasNewInvoices = true;
                $this->invoicer->sendInvoiceForRegistration($registration);

                $this->addFlash(
                    'success',
                    sprintf(
                        'Faktura pro %s %s byla vytvořena a poslána',
                        $registration->getTrainingName(),
                        $registration->getName()
                    )
                );
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
