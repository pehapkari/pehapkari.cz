<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use K0nias\FakturoidApi\Api;
use K0nias\FakturoidApi\Http\Request\Invoice\CreateInvoiceRequest;
use K0nias\FakturoidApi\Http\Request\Invoice\GetInvoiceRequest;
use K0nias\FakturoidApi\Http\Request\Message\CreateMessageRequest;
use K0nias\FakturoidApi\Http\Request\Subject\CreateSubjectRequest;
use K0nias\FakturoidApi\Http\Request\Subject\SearchSubjectsRequest;
use K0nias\FakturoidApi\Http\Response\Invoice\CreateInvoiceResponse;
use K0nias\FakturoidApi\Http\Response\Invoice\GetInvoiceResponse;
use K0nias\FakturoidApi\Http\Response\Message\CreateMessageResponse;
use K0nias\FakturoidApi\Http\Response\Subject\CreateSubjectResponse;
use K0nias\FakturoidApi\Http\Response\Subject\SearchSubjectsResponse;
use K0nias\FakturoidApi\Model\Filter\SearchParameters;
use K0nias\FakturoidApi\Model\Invoice\Id as InvoiceId;
use K0nias\FakturoidApi\Model\Invoice\Invoice;
use K0nias\FakturoidApi\Model\Invoice\OptionalParameters as InvoiceOptionalParameters;
use K0nias\FakturoidApi\Model\Line\Line;
use K0nias\FakturoidApi\Model\Line\LineCollection;
use K0nias\FakturoidApi\Model\Message\Message;
use K0nias\FakturoidApi\Model\Payment\Method;
use K0nias\FakturoidApi\Model\Subject\Id;
use K0nias\FakturoidApi\Model\Subject\Subject;
use Pehapkari\Exception\FakturoidException;
use Pehapkari\Registration\Api\Factory\SubjectApiObjectFactory;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Exception\MissingEnvValueException;

/**
 * @see https://fakturoid.docs.apiary.io
 */
final class FakturoidApi
{
    /**
     * @var int
     */
    private const INVOICE_PAYMENT_DAYS_DUE = 7;

    /**
     * @var string
     */
    private $fakturoidSlug;

    /**
     * @var string
     */
    private $fakturoidApiKey;

    /**
     * @var Api
     */
    private $koniasFakturoidApi;

    /**
     * @var SubjectApiObjectFactory
     */
    private $subjectApiObjectFactory;

    public function __construct(
        Api $koniasFakturoidApi,
        SubjectApiObjectFactory $subjectApiObjectFactory,
        string $fakturoidSlug,
        string $fakturoidApiKey
    ) {
        $this->koniasFakturoidApi = $koniasFakturoidApi;
        $this->subjectApiObjectFactory = $subjectApiObjectFactory;
        $this->fakturoidSlug = $fakturoidSlug;
        $this->fakturoidApiKey = $fakturoidApiKey;
    }

    public function createInvoice(TrainingRegistration $trainingRegistration): InvoiceId
    {
        $this->ensureEnvsAreSet();

        $subjectId = $this->getSubjectIdExistingOrCreated($trainingRegistration);

        $paymentMethod = new Method(Method::BANK_METHOD);
        $lineCollection = $this->createInvoiceLines($trainingRegistration);
        $invoiceOptionalParameters = new InvoiceOptionalParameters();
        $invoiceOptionalParameters->due(self::INVOICE_PAYMENT_DAYS_DUE);

        $invoice = new Invoice($subjectId, $paymentMethod, $lineCollection, $invoiceOptionalParameters);

        /** @var CreateInvoiceResponse $createInvoiceResponse */
        $createInvoiceResponse = $this->koniasFakturoidApi->process(new CreateInvoiceRequest($invoice));

        return $createInvoiceResponse->getId();
    }

    public function sendInvoiceEmail(InvoiceId $invoiceId): void
    {
        $createMessageRequest = new CreateMessageRequest(new Message($invoiceId));

        /** @var CreateMessageResponse $createMessageResponse */
        $createMessageResponse = $this->koniasFakturoidApi->process($createMessageRequest);

        if ($createMessageResponse->hasError()) {
            throw new FakturoidException(sprintf('Invoice email for invoice %d was not sent.', $invoiceId->getId()));
        }
    }

    public function isInvoicePaid(int $invoiceId): bool
    {
        $getInvoiceRequest = new GetInvoiceRequest(new InvoiceId($invoiceId));

        /** @var GetInvoiceResponse $getInvoiceResponse */
        $getInvoiceResponse = $this->koniasFakturoidApi->process($getInvoiceRequest);
        $invoice = $getInvoiceResponse->getInvoice();

        if (isset($invoice['paid_at']) && $invoice['paid_at']) {
            if ((float) $invoice['paid_amount'] >= (float) $invoice['total']) {
                return true;
            }
        }

        return false;
    }

    private function ensureEnvsAreSet(): void
    {
        // ensure ENVs are set, the fakturoid 3rd arty package doesn't check this (pain)
        if ($this->fakturoidSlug === '') {
            throw new MissingEnvValueException(sprintf('Complete "%s" in ".env.local" for dev or', 'FAKTUROID_SLUG'));
        }
        if ($this->fakturoidApiKey === '') {
            throw new MissingEnvValueException(sprintf(
                'Complete "%s" in ".env.local" for dev or',
                'FAKTUROID_API_KEY'
            ));
        }
    }

    private function getSubjectIdExistingOrCreated(TrainingRegistration $trainingRegistration): Id
    {
        // find subject by ICO
        $existingSubject = $this->findSubjectByIco($trainingRegistration->getIco());

        // we found the subject
        if ($existingSubject) {
            return new Id($existingSubject['id']);
        }

        $subject = $this->subjectApiObjectFactory->createFromTrainingsRegistration($trainingRegistration);

        /** @var CreateSubjectResponse $createSubjectResponse */
        $createSubjectResponse = $this->koniasFakturoidApi->process(new CreateSubjectRequest($subject));

        return $createSubjectResponse->getSubjectId();
    }

    private function createInvoiceLines(TrainingRegistration $trainingRegistration): LineCollection
    {
        return new LineCollection([
            new Line(
                'Školení ' . $trainingRegistration->getTrainingName(),
                (float) $trainingRegistration->getPrice(),
                (float) $trainingRegistration->getParticipantCount(),
                'ks'
            ),
        ]);
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

        return $searchSubjectResponse->getSubjects()[0] ?? null;
    }
}
