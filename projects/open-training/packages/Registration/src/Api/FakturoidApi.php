<?php declare(strict_types=1);

namespace OpenTraining\Registration\Api;

use Defr\Ares;
use K0nias\FakturoidApi\Api;
use K0nias\FakturoidApi\Http\Request\Invoice\CreateInvoiceRequest;
use K0nias\FakturoidApi\Http\Request\Subject\CreateSubjectRequest;
use K0nias\FakturoidApi\Http\Request\Subject\SearchSubjectsRequest;
use K0nias\FakturoidApi\Http\Response\Subject\CreateSubjectResponse;
use K0nias\FakturoidApi\Http\Response\Subject\SearchSubjectsResponse;
use K0nias\FakturoidApi\Model\Filter\SearchParameters;
use K0nias\FakturoidApi\Model\Invoice\Invoice;
use K0nias\FakturoidApi\Model\Invoice\OptionalParameters as InvoiceOptionalParameters;
use K0nias\FakturoidApi\Model\Line\Line;
use K0nias\FakturoidApi\Model\Line\LineCollection;
use K0nias\FakturoidApi\Model\Payment\Method;
use K0nias\FakturoidApi\Model\Subject\Id;
use K0nias\FakturoidApi\Model\Subject\OptionalParameters;
use K0nias\FakturoidApi\Model\Subject\Subject;
use OpenTraining\Registration\Entity\TrainingRegistration;

/**
 * @see https://fakturoid.docs.apiary.io
 */
final class FakturoidApi
{
    /**
     * @var Api
     */
    private $koniasFakturoidApi;

    /**
     * @var Ares
     */
    private $ares;

    public function __construct(Api $koniasFakturoidApi, Ares $ares)
    {
        $this->koniasFakturoidApi = $koniasFakturoidApi;
        $this->ares = $ares;
    }

    public function createInvoice(TrainingRegistration $trainingRegistration): void
    {
        $subjectId = $this->getSubjectIdExistingOrCreated($trainingRegistration);

        $paymentMethod = new Method(Method::BANK_METHOD);

        $lineCollection = new LineCollection([
            new Line(
                'Školení ' . $trainingRegistration->getTrainingName(),
                $trainingRegistration->getPrice(),
                1.0 /* @todo $trainingRegistration->getParticipantCount()*/,
                'ks'
            ),
        ]);

        $invoiceOptionalParameters = new InvoiceOptionalParameters();
        $invoiceOptionalParameters->due(7);

        $invoice = new Invoice($subjectId, $paymentMethod, $lineCollection, $invoiceOptionalParameters);

        $this->koniasFakturoidApi->process(new CreateInvoiceRequest($invoice));
    }

    private function getSubjectIdExistingOrCreated(TrainingRegistration $trainingRegistration): Id
    {
        // find subject by ICO
        $existingSubject = $this->findSubjectByIco($trainingRegistration->getIco());

        // we found the subject
        if ($existingSubject) {
            return new Id($existingSubject['id']);
        }

        // create subject
        $subjectOptionalParameters = new OptionalParameters();
        if ($trainingRegistration->getEmail()) {
            $subjectOptionalParameters->email($trainingRegistration->getEmail());
        }

        if ($trainingRegistration->getPhone()) {
            $subjectOptionalParameters->phone($trainingRegistration->getPhone());
        }

        $name = $trainingRegistration->getName();
        if ($trainingRegistration->getIco()) {
            $subjectOptionalParameters->registrationNumber($trainingRegistration->getIco());
            $aresRecord = $this->ares->findByIdentificationNumber($trainingRegistration->getIco());

            $subjectOptionalParameters->street($aresRecord->getStreetWithNumbers());
            $subjectOptionalParameters->city($aresRecord->getTown());
            $subjectOptionalParameters->zip($aresRecord->getZip());

            if ($aresRecord->getTaxId()) {
                $subjectOptionalParameters->vatNumber($aresRecord->getTaxId());
            }

            $name = $aresRecord->getCompanyName();
        }

        $subject = new Subject($name, $subjectOptionalParameters);

        /** @var CreateSubjectResponse $createSubjectResponse */
        $createSubjectResponse = $this->koniasFakturoidApi->process(new CreateSubjectRequest($subject));

        return $createSubjectResponse->getSubjectId();
    }

    /**
     * @return mixed[]|null
     */
    private function findSubjectByIco(?string $ico): ?array
    {
        if ($ico === null) {
            return null;
        }

        $searchParameters = new SearchParameters();
        $searchParameters->query($ico);
        $searchSubjectsRequest = new SearchSubjectsRequest($searchParameters);

        /** @var SearchSubjectsResponse $searchSubjectResponse */
        $searchSubjectResponse = $this->koniasFakturoidApi->process($searchSubjectsRequest);

        if ($searchSubjectResponse->getSubjects()) {
            return $searchSubjectResponse->getSubjects()[0];
        }

        return null;
    }
}
