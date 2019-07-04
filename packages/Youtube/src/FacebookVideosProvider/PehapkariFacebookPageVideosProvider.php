<?php declare(strict_types=1);

namespace Pehapkari\Youtube\FacebookVideosProvider;

use Facebook\Facebook;
use Nette\Utils\Json;
use Pehapkari\Marketing\Social\FacebookIds;
use Pehapkari\Youtube\Contract\FacebookVideosProvider\FacebookVideosProviderInterface;

/**
 * @todo finish after FB api token for page is accepted
 */
final class PehapkariFacebookPageVideosProvider implements FacebookVideosProviderInterface
{
    /**
     * @var Facebook
     */
    private $facebook;

    public function __construct(Facebook $facebook)
    {
        $this->facebook = $facebook;
    }

    public function getName(): string
    {
        return 'videos';
    }

    /**
     * @return mixed[]
     */
    public function providePlaylists(): array
    {
        return [];

        // https://developers.facebook.com/docs/graph-api/reference/page/video_lists/
        $endPoint = FacebookIds::PEHAPKARI_PAGE_ID . '/video_lists';
        $response = $this->facebook->get($endPoint);

        $data = Json::decode($response->getBody(), Json::FORCE_ARRAY)['data'];

        foreach ($data as $item) {
            // https://developers.facebook.com/docs/graph-api/reference/video-list/
            $endPoint = $item['id'] . '?fields=videos';
            $response = $this->facebook->get($endPoint);

            $data = Json::decode($response->getBody(), Json::FORCE_ARRAY);
        }

        return $data;
    }
}
