<?php

declare(strict_types=1);

namespace Pehapkari\ValueObject;

/**
 * Helpful object to autocomplete in twig files
 */
final class Organizer
{
    public string $photo;

    public string $name;

    public string $responsibility;

    public ?string $url = null;

    public ?string $company = null;

    public ?string $company_url = null;
}
