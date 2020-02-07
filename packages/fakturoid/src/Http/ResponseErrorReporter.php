<?php

declare(strict_types=1);

namespace Pehapkari\Fakturoid\Http;

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

    public function reportInvalidResponse(ResponseInterface $response, string $endpoint): void
    {
        if ($response->getStatusCode() < 400) {
            return;
        }

        $responseData = $this->requestResponseFormatter->formatResponseToArray($response);

        $errorsString = sprintf(
            'Endpoint "%s" failed with code %d because "%s"',
            $endpoint,
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        if (isset($responseData['errors'])) {
            $errorsString .= PHP_EOL . PHP_EOL;

            foreach ($responseData['errors'] as $key => $keyErrors) {
                $errorsString .= '* ' . $key . ': ' . implode(', ', $keyErrors) . PHP_EOL;
            }
        }

        throw new ShouldNotHappenException($errorsString);
    }
}
