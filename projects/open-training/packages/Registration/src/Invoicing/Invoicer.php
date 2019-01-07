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
        $this->fakturoidApi->createInvoice($trainingRegistration);

        // update registration about invoice
        $trainingRegistration->setHasInvoice(true);
        $this->trainingRegistrationRepository->save($trainingRegistration);

        // @todo send invoice
    }
}
