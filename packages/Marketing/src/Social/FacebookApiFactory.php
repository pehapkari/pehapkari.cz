<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Social;

use Facebook\Facebook;

final class FacebookApiFactory
{
    /**
     * @var string
     * On 2019-06-11
     */
    private const API_VERSION = 'v3.3';

    /**
     * @var string
     */
    private $facebookAppId;

    /**
     * @var string
     */
    private $facebookAppSecret;

    /**
     * @var string
     */
    private $facebookAccessToken;

    public function __construct(string $facebookAppId, string $facebookAppSecret, string $facebookAccessToken)
    {
        $this->facebookAppId = $facebookAppId;
        $this->facebookAppSecret = $facebookAppSecret;
        $this->facebookAccessToken = $facebookAccessToken;
    }

    public function create(): Facebook
    {
        return new Facebook([
            'app_id' => $this->facebookAppId,
            'app_secret' => $this->facebookAppSecret,
            'default_graph_version' => self::API_VERSION,
            'default_access_token' => $this->facebookAccessToken,
        ]);

        // how to get access token :)
        // - "manage_pages" permission is needed to post on a page
        // - to get that, you need to review app https://developers.facebook.com/docs/apps/review
    }
}
