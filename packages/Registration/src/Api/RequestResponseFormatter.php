<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;

final class RequestResponseFormatter
{
    /**
     * @return mixed[]
     */
    public function formatResponseToArray(ResponseInterface $response): array
    {
        $responseContent = $response->getBody()->getContents();

        // prevent json format errors
        if ($responseContent === '') {
            return [];
        }

        return Json::decode($responseContent, Json::FORCE_ARRAY);
    }
}
