<?php

declare(strict_types=1);

namespace Pehapkari\Fakturoid\Guzzle;

use GuzzleHttp\Client;
use Pehapkari\Fakturoid\Http\RequestResponseFormatter;
use Pehapkari\Fakturoid\Http\ResponseErrorReporter;

final class FakturoidClient extends Client
{
    private ResponseErrorReporter $responseErrorReporter;

    private RequestResponseFormatter $requestResponseFormatter;

    public function __construct(
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
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function requestToJson(string $method, string $uri = '', array $options = []): array
    {
        $response = parent::request($method, $uri, $options);

        $this->responseErrorReporter->reportInvalidResponse($response, $uri);

        return $this->requestResponseFormatter->formatResponseToArray($response);
    }
}
