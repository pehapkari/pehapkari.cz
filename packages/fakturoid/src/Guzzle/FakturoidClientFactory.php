<?php

declare(strict_types=1);

namespace Pehapkari\Fakturoid\Guzzle;

use Pehapkari\Fakturoid\Http\RequestResponseFormatter;
use Pehapkari\Fakturoid\Http\ResponseErrorReporter;
use Pehapkari\Registration\Exception\MissingEnvValueException;

final class FakturoidClientFactory
{
    private string $fakturoidSlug;

    private string $fakturoidApiKey;

    private ResponseErrorReporter $responseErrorReporter;

    private RequestResponseFormatter $requestResponseFormatter;

    public function __construct(
        string $fakturoidSlug,
        string $fakturoidApiKey,
        ResponseErrorReporter $responseErrorReporter,
        RequestResponseFormatter $requestResponseFormatter
    ) {
        $this->fakturoidSlug = $fakturoidSlug;
        $this->fakturoidApiKey = $fakturoidApiKey;
        $this->responseErrorReporter = $responseErrorReporter;
        $this->requestResponseFormatter = $requestResponseFormatter;
    }

    public function create(): FakturoidClient
    {
        $this->ensureEnvsAreSet($this->fakturoidSlug, $this->fakturoidApiKey);

        return new FakturoidClient(
            $this->fakturoidApiKey,
            $this->responseErrorReporter,
            $this->requestResponseFormatter
        );
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
}
