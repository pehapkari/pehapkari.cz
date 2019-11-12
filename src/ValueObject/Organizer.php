<?php

declare(strict_types=1);

namespace Pehapkari\ValueObject;

/**
 * Helpful object to autocomplete in twig files
 */
final class Organizer
{
    /**
     * @var string
     */
    public $photo;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $responsibility;

    /**
     * @var string|null
     */
    public $url;

    /**
     * @var string|null
     */
    public $company;

    /**
     * @var string|null
     */
    public $company_url;
}
