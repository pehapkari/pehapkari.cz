<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use Pehapkari\Exception\ShouldNotHappenException;
use Psr\Http\Message\ResponseInterface;

final class ResponseErrorReporter
{
    /**
     * @var RequestResponseFormatter
     */
    private $requestResponseFormatter;

    public function __construct(RequestResponseFormatter $requestResponseFormatter)
    {
        $this->requestResponseFormatter = $requestResponseFormatter;
    }

    /**
     * @param mixed[] $response
     */
    public function reportInvalidResponse(ResponseInterface $response, string $endpoint): void
    {
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            return;
        }

        $responseData = $this->requestResponseFormatter->formatResponseToArray($response);

        $errorsString = sprintf('Endpoint: "%s"', $endpoint . PHP_EOL . PHP_EOL);
        foreach ($responseData['errors'] as $key => $keyErrors) {
            $errorsString .= '* ' . $key . ': ' . implode(', ', $keyErrors) . PHP_EOL;
        }

        throw new ShouldNotHappenException($errorsString);
    }
}
