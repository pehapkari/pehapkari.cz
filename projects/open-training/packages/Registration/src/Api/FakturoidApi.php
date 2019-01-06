<?php declare(strict_types=1);

namespace OpenTraining\Registration\Api;

use K0nias\FakturoidApi\Api;

/**
 * @see https://fakturoid.docs.apiary.io
 */
final class FakturoidApi
{
    /**
     * @var string
     */
    private const API_MAIN_URL = 'https://app.fakturoid.cz/api/v2';

    /**
     * @var Api
     */
    private $koniasApi;

    public function __construct(string $fakturoidSlug, string $fakturoidEmail, string $fakturoidApiKey)
    {
        $this->koniasApi = new Api($fakturoidSlug, $fakturoidEmail, $fakturoidApiKey);
    }

    public function createInvoice()
    {
        dump($this->koniasApi->process());
        dump($this->koniasApi);
    }

}
