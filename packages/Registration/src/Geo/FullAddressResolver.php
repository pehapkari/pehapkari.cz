<?php declare(strict_types=1);

namespace Pehapkari\Registration\Geo;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Nette\Utils\Json;

final class FullAddressResolver
{
    /**
     * @var string
     */
    private const API_ADDRESS_DETAIL = 'https://nominatim.openstreetmap.org/search.php?q=%s&format=json';

    /**
     * @var string
     */
    private const API_LAT_LON_DETAIL = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=%s&lon=%s';

    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed[]
     */
    public function resolve(string $address): array
    {
        $address = urlencode($address);
        $url = sprintf(self::API_ADDRESS_DETAIL, $address);
        $request = new Request('GET', $url);
        $response = $this->client->send($request);
        $json = Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);

        $url = sprintf(self::API_LAT_LON_DETAIL, $json[0]['lat'], $json[0]['lon']);
        $request = new Request('GET', $url);
        $response = $this->client->send($request);
        $json = Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);

        return $json['address'];
    }
}
