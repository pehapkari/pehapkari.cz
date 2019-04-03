<?php declare(strict_types=1);

namespace Pehapkari\Registration\Api;

use K0nias\FakturoidApi\Api;

final class KoniasFakturoidApiFactory
{
    /**
     * @var string
     */
    private $fakturoidSlug;

    /**
     * @var string
     */
    private $fakturoidEmail;

    /**
     * @var string
     */
    private $fakturoidApiKey;

    public function __construct(string $fakturoidSlug, string $fakturoidEmail, string $fakturoidApiKey)
    {
        $this->fakturoidSlug = $fakturoidSlug;
        $this->fakturoidEmail = $fakturoidEmail;
        $this->fakturoidApiKey = $fakturoidApiKey;
    }

    public function create(): Api
    {
        return new Api($this->fakturoidSlug, $this->fakturoidEmail, $this->fakturoidApiKey);
    }
}
