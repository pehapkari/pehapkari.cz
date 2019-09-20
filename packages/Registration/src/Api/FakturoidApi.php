<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use GuzzleHttp\Client;
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
     * @var Client
     */
    private $guzzleFakturoidClient;

    /**
     * @var RequestResponseFormatter
     */
    private $requestResponseFormatter;

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
        RequestResponseFormatter $requestResponseFormatter,
        FakturoidClient $fakturoidClient,
        InvoiceDataFactory $invoiceDataFactory
    ) {
        $this->fakturoidSlug = $fakturoidSlug;
        $this->requestResponseFormatter = $requestResponseFormatter;
        $this->fakturoidClient = $fakturoidClient;
        $this->invoiceDataFactory = $invoiceDataFactory;
    }

    public function createInvoice(TrainingRegistration $trainingRegistration): int
    {
        $requestData = $this->invoiceDataFactory->createFromTrainingRegistration($trainingRegistration);

        $response = $this->fakturoidClient->request(
            'POST',
            sprintf(FakturoidEndpoint::POST_NEW_INVOICE, $this->fakturoidSlug),
            ['json' => $requestData]
        );

        $invoice = $this->requestResponseFormatter->formatResponseToArray($response);

        return $invoice['id'];
    }

    public function isInvoicePaid(int $invoiceId): bool
    {
        $endpoint = sprintf(FakturoidEndpoint::GET_INVOICE_DETAIL, $this->fakturoidSlug, $invoiceId);

        $response = $this->guzzleFakturoidClient->request('GET', $endpoint);

        $invoice = $this->requestResponseFormatter->formatResponseToArray($response);
        if (isset($invoice['paid_at']) && $invoice['paid_at']) {
            return ((float) $invoice['paid_amount']) >= ((float) $invoice['total']);
        }

        return false;
    }
}
