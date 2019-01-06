<?php declare(strict_types=1);

namespace OpenTraining\Registration\Invoicing;

use K0nias\FakturoidApi\Api;
use OpenTraining\Registration\Api\FakturoidApi;
use OpenTraining\Registration\Entity\TrainingRegistration;

final class Invoicer
{
    /**
     * @var FakturoidApi
     */
    private $fakturoidApi;

    public function __construct(FakturoidApi $fakturoidApi)
    {
        $this->fakturoidApi = $fakturoidApi;
    }

    public function sendInvoiceForRegistration(TrainingRegistration $registration)
    {



        $this->fakturoidApi->createInvoice();


        dump($registration);

        die;
    }
}
