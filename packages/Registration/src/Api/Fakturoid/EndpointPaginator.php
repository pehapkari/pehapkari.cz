<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api\Fakturoid;

use Nette\Utils\Strings;
use Psr\Http\Message\ResponseInterface;

final class EndpointPaginator
{
    /**
     * @var string
     */
    private const NEXT_PAGE_PATTERN = '#\<(?<link>.*?)\>\; rel\=\"next\"#';

    /**
     * @see https://fakturoid.docs.apiary.io/#introduction/strankovani
     */
    public function resolveNextPageEndpoint(ResponseInterface $response): ?string
    {
        $link = $response->getHeader('link')[0] ?? null;
        if ($link === null) {
            return null;
        }

        $pageLinks = explode(', ', $link);

        foreach ($pageLinks as $pageLink) {
            $match = Strings::match($pageLink, self::NEXT_PAGE_PATTERN);
            if (isset($match['link'])) {
                return $match['link'];
            }
        }

        return null;
    }
}
