<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use GuzzleHttp\Client;
use Nette\Utils\Json;
use Pehapkari\Exception\FakturoidException;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Registration\Api\Factory\SubjectDataFactory;
use Pehapkari\Registration\Api\Fakturoid\EndpointPaginator;
use Pehapkari\Registration\Api\Fakturoid\FakturoidEndpoints;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\Registration\Exception\MissingEnvValueException;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\build_query;

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

    /**
     * @var EndpointPaginator
     */
    private $endpointPaginator;

    public function __construct(
        string $fakturoidSlug,
        string $fakturoidApiKey,
        SubjectDataFactory $subjectDataFactory,
        EndpointPaginator $endpointPaginator
    ) {
        $this->ensureEnvsAreSet($fakturoidSlug, $fakturoidApiKey);

        $this->fakturoidSlug = $fakturoidSlug;
        $this->subjectDataFactory = $subjectDataFactory;

        $this->guzzleFakturoidClient = new Client([
            'auth' => ['tomas.vot@gmail.com', $fakturoidApiKey],
            'http_errors' => true,
        ]);

        $this->endpointPaginator = $endpointPaginator;
    }

    public function createInvoice(TrainingRegistration $trainingRegistration): int
    {
        $invoiceData = [
            'subject_id' => $this->getSubjectIdExistingOrCreated($trainingRegistration),
            'payment_method' => 'bank',
            'due' => self::INVOICE_PAYMENT_DAYS_DUE, // number days to pay invoice in
            'lines' => [
                [
                    'Školení ' . $trainingRegistration->getTrainingName(),
                    (float) $trainingRegistration->getPrice(),
                    (int) $trainingRegistration->getParticipantCount(),
                    'osob',
                ],
            ],
        ];

        // @todo test
        $endpoint = sprintf(FakturoidEndpoints::NEW_INVOICE, $this->fakturoidSlug);
        $endpoint .= '?' . build_query($invoiceData);

        $response = $this->guzzleFakturoidClient->request('POST', $endpoint);

        return $this->getJsonFromResponse($response)['id'];
    }

    public function sendInvoiceEmail(int $invoiceId): void
    {
        $endpoint = sprintf(FakturoidEndpoints::INVOICE_ACTION, $this->fakturoidSlug, $invoiceId);
        $endpoint .= '?event=deliver';

        $response = $this->guzzleFakturoidClient->request('POST', $endpoint);

        if ($response->getStatusCode() !== 200) {
            throw new FakturoidException(sprintf('Invoice email for invoice %d was not sent.', $invoiceId));
        }
    }

    public function isInvoicePaid(int $invoiceId): bool
    {
        $endpoint = sprintf(FakturoidEndpoints::INVOICE_DETAIL, $this->fakturoidSlug, $invoiceId);

        $response = $this->guzzleFakturoidClient->request('GET', $endpoint);

        $invoice = $this->getJsonFromResponse($response);
        if (isset($invoice['paid_at']) && $invoice['paid_at']) {
            return ((float) $invoice['paid_amount']) >= ((float) $invoice['total']);
        }

        return false;
    }

    /**
     * @return mixed[]
     */
    public function getInvoicesBySlug(string $slug): array
    {
        $endpoint = sprintf(FakturoidEndpoints::INVOICES, $slug);

        $invoices = [];
        do {
            $response = $this->guzzleFakturoidClient->request('GET', $endpoint);
            $newInvoices = $this->getJsonFromResponse($response);

            $invoices = array_merge($invoices, $newInvoices);

            $endpoint = $this->endpointPaginator->resolveNextPageEndpoint($response);
        } while ($endpoint !== null);

        return $invoices;
    }

    /**
     * @return mixed[]
     */
    public function getSubjectByUrl(string $url): array
    {
        $response = $this->guzzleFakturoidClient->request('GET', $url);

        return $this->getJsonFromResponse($response);
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

    private function getSubjectIdExistingOrCreated(TrainingRegistration $trainingRegistration): int
    {
        // find subject by ICO
        $existingSubject = $this->findSubjectByIco($trainingRegistration->getIco());

        // we found the subject
        if ($existingSubject) {
            return (int) $existingSubject['id'];
        }

        $endpoint = sprintf(FakturoidEndpoints::NEW_CONTACT, $this->fakturoidSlug);

        $subjectData = $this->subjectDataFactory->createFromTrainingRegistration($trainingRegistration);
        $endpoint .= build_query($subjectData);

        $response = $this->guzzleFakturoidClient->request('POST', $endpoint);

        $data = $this->getJsonFromResponse($response);
        if (! isset($data['id'])) {
            throw new ShouldNotHappenException();
        }

        return (int) $data['id'];
    }

    /**
     * @return mixed[]
     */
    private function getJsonFromResponse(ResponseInterface $response): array
    {
        return Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);
    }

    /**
     * @return mixed[]|null
     */
    private function findSubjectByIco(?string $ico): ?array
    {
        if ($ico === null) {
            return null;
        }

        $endpoint = sprintf(FakturoidEndpoints::SEARCH_CONTACT, $this->fakturoidSlug, $ico);
        $response = $this->guzzleFakturoidClient->request('GET', $endpoint);

        return $this->getJsonFromResponse($response)['subjects'][0] ?? null;
    }
}
