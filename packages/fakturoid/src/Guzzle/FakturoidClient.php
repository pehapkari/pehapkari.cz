<?php

declare(strict_types=1);

namespace Pehapkari\Fakturoid\Guzzle;

use GuzzleHttp\Client;
use Pehapkari\Fakturoid\Http\RequestResponseFormatter;
use Pehapkari\Fakturoid\Http\ResponseErrorReporter;
use Pehapkari\Registration\Exception\MissingEnvValueException;

final class FakturoidClient extends Client
{
    private ResponseErrorReporter $responseErrorReporter;

    private RequestResponseFormatter $requestResponseFormatter;

    private string $fakturoidApiKey;

    private string $fakturoidSlug;

    public function __construct(
        string $fakturoidSlug,
        string $fakturoidApiKey,
        ResponseErrorReporter $responseErrorReporter,
        RequestResponseFormatter $requestResponseFormatter
    ) {
        parent::__construct([
            'auth' => ['tomas.vot@gmail.com', $fakturoidApiKey],
            'http_errors' => false,
        ]);

        $this->responseErrorReporter = $responseErrorReporter;
        $this->requestResponseFormatter = $requestResponseFormatter;

        $this->fakturoidApiKey = $fakturoidApiKey;
        $this->fakturoidSlug = $fakturoidSlug;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function requestToJson(string $method, string $uri = '', array $options = []): array
    {
        $this->ensureEnvsAreSet($this->fakturoidSlug, $this->fakturoidApiKey);

        $response = parent::request($method, $uri, $options);

        $this->responseErrorReporter->reportInvalidResponse($response, $uri);

        return $this->requestResponseFormatter->formatResponseToArray($response);
    }

    private function ensureEnvsAreSet(string $fakturoidSlug, string $fakturoidApiKey): void
    {
        // ensure ENVs are set, the fakturoid 3rd arty package doesn't check this (pain)
        if ($fakturoidSlug === '') {
            throw new MissingEnvValueException(sprintf(
                'Complete "%s" in ".env.local" for dev or to "docker-compose.yml" on production server',
                'FAKTUROID_SLUG'
            ));
        }

        if ($fakturoidApiKey === '') {
            throw new MissingEnvValueException(sprintf(
                'Complete "%s" in ".env.local" for dev or to "docker-compose.yml" on production server',
                'FAKTUROID_API_KEY'
            ));
        }
    }
}
