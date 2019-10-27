<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Invoicing;

use Pehapkari\Fakturoid\FakturoidApi;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Repository\RegistrationRepository;

final class Invoicer
{
    /**
     * @var FakturoidApi
     */
    private $fakturoidApi;

    /**
     * @var RegistrationRepository
     */
    private $trainingRegistrationRepository;

    public function __construct(
        FakturoidApi $fakturoidApi,
        RegistrationRepository $trainingRegistrationRepository
    ) {
        $this->fakturoidApi = $fakturoidApi;
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
    }

    public function createInvoiceForRegistration(TrainingRegistration $trainingRegistration): void
    {
        $invoiceId = $this->fakturoidApi->createInvoice($trainingRegistration);

        // se we can pair paid invoices
        $trainingRegistration->setFakturoidInvoiceId($invoiceId);

        // update registration about invoice
        $trainingRegistration->setHasInvoice(true);
        $this->trainingRegistrationRepository->save($trainingRegistration);
    }
}
