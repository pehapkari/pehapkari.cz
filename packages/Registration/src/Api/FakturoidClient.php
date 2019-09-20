<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use GuzzleHttp\Client;

final class FakturoidClient extends Client
{
    /**
     * @var ResponseErrorReporter
     */
    private $responseErrorReporter;

    /**
     * @var RequestResponseFormatter
     */
    private $requestResponseFormatter;

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
     * @param string $method
     * @param string $uri
     * @param mixed[] $options
     * @return mixed[]
     */
    public function requestToJson($method, $uri = '', array $options = []): array
    {
        $response = parent::request($method, $uri, $options);

        $this->responseErrorReporter->reportInvalidResponse($response, $uri);

        return $this->requestResponseFormatter->formatResponseToArray($response);
    }
}
