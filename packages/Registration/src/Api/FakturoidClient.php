<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

final class FakturoidClient extends Client
{
    /**
     * @var ResponseErrorReporter
     */
    private $responseErrorReporter;

    public function __construct(string $fakturoidApiKey, ResponseErrorReporter $responseErrorReporter)
    {
        parent::__construct([
            'auth' => ['tomas.vot@gmail.com', $fakturoidApiKey],
            'http_errors' => false,
        ]);

        $this->responseErrorReporter = $responseErrorReporter;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param mixed[] $options
     */
    public function request($method, $uri = '', array $options = []): ResponseInterface
    {
        $response = parent::request($method, $uri, $options);

        $this->responseErrorReporter->reportInvalidResponse($response, $uri);

        return $response;
    }
}
