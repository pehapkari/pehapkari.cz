<?php declare(strict_types=1);

namespace OpenTraining\Registration\Invoicing;

use OpenTraining\Registration\Api\FakturoidApi;
use OpenTraining\Registration\Entity\TrainingRegistration;
use OpenTraining\Registration\Repository\TrainingRegistrationRepository;

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

    public function sendInvoiceForRegistration(TrainingRegistration $trainingRegistration): void
    {
        // create invoice
        $invoiceId = $this->fakturoidApi->createInvoice($trainingRegistration);

        // send email
        $this->fakturoidApi->sendInvoiceEmail($invoiceId);

        // se we can pair paid invoices
        $trainingRegistration->setFakturoidInvoiceId($invoiceId->getId());

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
