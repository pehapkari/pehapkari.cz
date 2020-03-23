<?php

declare(strict_types=1);

namespace Pehapkari\Fakturoid;

use Pehapkari\Fakturoid\Factory\InvoiceDataFactory;
use Pehapkari\Fakturoid\Guzzle\FakturoidClient;
use Pehapkari\Fakturoid\ValueObject\FakturoidEndpoint;
use Pehapkari\Registration\Entity\TrainingRegistration;

/**
 * @see https://fakturoid.docs.apiary.io
 */
final class FakturoidApi
{
    private string $fakturoidSlug;

    private FakturoidClient $fakturoidClient;

    private InvoiceDataFactory $invoiceDataFactory;

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
}
