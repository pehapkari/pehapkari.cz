<?php declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use Pehapkari\Registration\Invoicing\Invoicer;
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
}
