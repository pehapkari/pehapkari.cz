<?php

declare(strict_types=1);

namespace Pehapkari\Fakturoid\Guzzle;

use Pehapkari\Fakturoid\Http\RequestResponseFormatter;
use Pehapkari\Fakturoid\Http\ResponseErrorReporter;

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
        return new FakturoidClient(
            $this->fakturoidSlug,
            $this->fakturoidApiKey,
            $this->responseErrorReporter,
            $this->requestResponseFormatter
        );
    }
}
