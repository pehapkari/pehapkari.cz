<?php declare(strict_types=1);

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
        return Json::decode($response->getBody()->getContents(), Json::FORCE_ARRAY);
    }
}
