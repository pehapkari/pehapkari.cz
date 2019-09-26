<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api\Factory;

use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Registration\Api\Fakturoid\FakturoidEndpoint;
use Pehapkari\Registration\Api\FakturoidClient;
use Pehapkari\Registration\Entity\TrainingRegistration;

final class InvoiceDataFactory
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
     * @var FakturoidClient
     */
    private $fakturoidClient;

    /**
     * @var SubjectDataFactory
     */
    private $subjectDataFactory;

    public function __construct(
        string $fakturoidSlug,
        FakturoidClient $fakturoidClient,
        SubjectDataFactory $subjectDataFactory
    ) {
        $this->fakturoidClient = $fakturoidClient;
        $this->subjectDataFactory = $subjectDataFactory;
        $this->fakturoidSlug = $fakturoidSlug;
    }

    /**
     * @return mixed[]
     */
    public function createFromTrainingRegistration(TrainingRegistration $trainingRegistration): array
    {
        return [
            'subject_id' => $this->getSubjectIdExistingOrCreated($trainingRegistration),
            'payment_method' => 'bank',
            'currency' => 'CZK',
            'due' => self::INVOICE_PAYMENT_DAYS_DUE, // number days to pay invoice in
            'lines' => [
                [
                    'name' => 'Školení ' . $trainingRegistration->getTrainingName(),
                    'unit_price' => (float) $trainingRegistration->getPrice(),
                    'quantity' => (int) $trainingRegistration->getParticipantCount(),
                    'unit_name' => 'osob',
                ],
            ],
        ];
    }

    private function getSubjectIdExistingOrCreated(TrainingRegistration $trainingRegistration): int
    {
        // find subject by ICO
        $existingSubject = $this->findSubjectByIco($trainingRegistration->getIco());

        // we found the subject
        if ($existingSubject) {
            return (int) $existingSubject['id'];
        }

        $endpoint = sprintf(FakturoidEndpoint::POST_NEW_CONTACT, $this->fakturoidSlug);
        $requestData = $this->subjectDataFactory->createFromTrainingRegistration($trainingRegistration);

        $responseData = $this->fakturoidClient->requestToJson('POST', $endpoint, [
            'json' => $requestData,
        ]);

        if (! isset($responseData['id'])) {
            throw new ShouldNotHappenException();
        }

        return (int) $responseData['id'];
    }

    /**
     * @return mixed[]|null
     */
    private function findSubjectByIco(?string $ico): ?array
    {
        if ($ico === null) {
            return null;
        }

        $ico = trim($ico);

        $endpoint = sprintf(FakturoidEndpoint::GET_SEARCH_CONTACT, $this->fakturoidSlug, $ico);
        $subjectsData = $this->fakturoidClient->requestToJson('GET', $endpoint);

        return $subjectsData['subjects'][0] ?? null;
    }
}
