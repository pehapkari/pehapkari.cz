<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use Pehapkari\Registration\Api\Factory\InvoiceDataFactory;
use Pehapkari\Registration\Api\Fakturoid\FakturoidEndpoint;
use Pehapkari\Registration\Entity\TrainingRegistration;

/**
 * @see https://fakturoid.docs.apiary.io
 */
final class FakturoidApi
{
    /**
     * @var string
     */
    private $fakturoidSlug;

    /**
     * @var FakturoidClient
     */
    private $fakturoidClient;

    /**
     * @var InvoiceDataFactory
     */
    private $invoiceDataFactory;

    public function __construct(
        string $fakturoidSlug,
        FakturoidClient $fakturoidClient,
        InvoiceDataFactory $invoiceDataFactory
    ) {
        $this->fakturoidSlug = $fakturoidSlug;
        $this->fakturoidClient = $fakturoidClient;
        $this->invoiceDataFactory = $invoiceDataFactory;
    }

    public function createInvoice(TrainingRegistration $trainingRegistration): int
    {
        $requestData = $this->invoiceDataFactory->createFromTrainingRegistration($trainingRegistration);

        $invoice = $this->fakturoidClient->requestToJson(
            'POST',
            sprintf(FakturoidEndpoint::POST_NEW_INVOICE, $this->fakturoidSlug),
            ['json' => $requestData]
        );

        return $invoice['id'];
    }

    public function isInvoicePaid(int $invoiceId): bool
    {
        $endpoint = sprintf(FakturoidEndpoint::GET_INVOICE_DETAIL, $this->fakturoidSlug, $invoiceId);

        $invoice = $this->fakturoidClient->requestToJson('GET', $endpoint);

        if (isset($invoice['paid_at']) && $invoice['paid_at']) {
            return ((float) $invoice['paid_amount']) >= ((float) $invoice['total']);
        }

        return false;
    }
}
