<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use GuzzleHttp\Client;
use Nette\Utils\Json;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Registration\Api\Factory\SubjectDataFactory;
use Pehapkari\Registration\Api\Fakturoid\FakturoidEndpoint;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Exception\MissingEnvValueException;
use Psr\Http\Message\ResponseInterface;

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
     * @var Client
     */
    private $guzzleFakturoidClient;

    /**
     * @var SubjectDataFactory
     */
    private $subjectDataFactory;

    public function __construct(
        string $fakturoidSlug,
        string $fakturoidApiKey,
        SubjectDataFactory $subjectDataFactory
    ) {
        $this->ensureEnvsAreSet($fakturoidSlug, $fakturoidApiKey);

        $this->fakturoidSlug = $fakturoidSlug;
        $this->subjectDataFactory = $subjectDataFactory;

        $this->guzzleFakturoidClient = new Client([
            'auth' => ['tomas.vot@gmail.com', $fakturoidApiKey],
            'http_errors' => false,
        ]);
    }

    public function createInvoice(TrainingRegistration $trainingRegistration): int
    {
        $requestData = $this->createInvoiceRequestData($trainingRegistration);

        $response = $this->guzzleFakturoidClient->request(
            'POST',
            sprintf(FakturoidEndpoint::POST_NEW_INVOICE, $this->fakturoidSlug),
            ['json' => $requestData]
        );

        return $this->getJsonFromResponse($response)['id'];
    }

    public function isInvoicePaid(int $invoiceId): bool
    {
        $endpoint = sprintf(FakturoidEndpoint::GET_INVOICE_DETAIL, $this->fakturoidSlug, $invoiceId);

        $response = $this->guzzleFakturoidClient->request('GET', $endpoint);

        $invoice = $this->getJsonFromResponse($response);
        if (isset($invoice['paid_at']) && $invoice['paid_at']) {
            return ((float) $invoice['paid_amount']) >= ((float) $invoice['total']);
        }

        return false;
    }

    private function ensureEnvsAreSet(string $fakturoidSlug, string $fakturoidApiKey): void
    {
        // ensure ENVs are set, the fakturoid 3rd arty package doesn't check this (pain)
        if ($fakturoidSlug === '') {
            throw new MissingEnvValueException(sprintf(
                'Complete "%s" in ".env.local" for dev or to "docker-composer.yml" on production server',
                'FAKTUROID_SLUG'
            ));
        }

        if ($fakturoidApiKey === '') {
            throw new MissingEnvValueException(sprintf(
                'Complete "%s" in ".env.local" for dev or to "docker-composer.yml" on production server',
                'FAKTUROID_API_KEY'
            ));
        }
    }

    /**
     * @return mixed[]
     */
    private function createInvoiceRequestData(TrainingRegistration $trainingRegistration): array
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

    /**
     * @return mixed[]
     */
    private function getJsonFromResponse(ResponseInterface $response): array
    {
        return Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);
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

        $response = $this->guzzleFakturoidClient->request('POST', $endpoint, [
            'json' => $requestData,
        ]);
        $responseData = $this->getJsonFromResponse($response);

        $this->reportInvalidResponse($response, $endpoint, $responseData);

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

        $endpoint = sprintf(FakturoidEndpoint::GET_SEARCH_CONTACT, $this->fakturoidSlug, $ico);
        $response = $this->guzzleFakturoidClient->request('GET', $endpoint);

        return $this->getJsonFromResponse($response)['subjects'][0] ?? null;
    }

    /**
     * @param mixed[] $responseData
     */
    private function reportInvalidResponse(ResponseInterface $response, string $endpoint, array $responseData): void
    {
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            return;
        }

        $errorsString = sprintf('Endpoint: "%s"', $endpoint . PHP_EOL . PHP_EOL);
        foreach ($responseData['errors'] as $key => $keyErrors) {
            $errorsString .= '* ' . $key . ': ' . implode(', ', $keyErrors) . PHP_EOL;
        }

        throw new ShouldNotHappenException($errorsString);
    }
}
