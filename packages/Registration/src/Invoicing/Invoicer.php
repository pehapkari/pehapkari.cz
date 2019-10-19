<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Invoicing;

use Pehapkari\Registration\Api\FakturoidApi;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Repository\TrainingRegistrationRepository;

final class Invoicer
{
    /**
     * @var FakturoidApi
     */
    private $fakturoidApi;

    /**
     * @var TrainingRegistrationRepository
     */
    private $trainingRegistrationRepository;

    public function __construct(
        FakturoidApi $fakturoidApi,
        TrainingRegistrationRepository $trainingRegistrationRepository
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

    public function syncPaidInvoices(): int
    {
        $unpaidRegistrations = $this->trainingRegistrationRepository->getUnpaid();
        $updatedRegistrationsCount = 0;

        foreach ($unpaidRegistrations as $unpaidRegistration) {
            if ($unpaidRegistration->getFakturoidInvoiceId() === null) {
                continue;
            }

            if ($this->fakturoidApi->isInvoicePaid($unpaidRegistration->getFakturoidInvoiceId())) {
                $unpaidRegistration->setIsPaid(true);
                $this->trainingRegistrationRepository->save($unpaidRegistration);
                ++$updatedRegistrationsCount;
            }
        }

        return $updatedRegistrationsCount;
    }
}
