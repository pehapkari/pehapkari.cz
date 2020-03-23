<?php

declare(strict_types=1);

namespace Pehapkari\ValueObject;

use DateTimeInterface;

final class Meetup
{
    private string $name;

    private string $city;

    private string $url;

    private string $country;

    private DateTimeInterface $startDateTime;

    public function __construct(
        string $name,
        DateTimeInterface $startDateTime,
        string $city,
        string $url,
        string $country
    ) {
        $this->name = $name;
        $this->startDateTime = $startDateTime;
        $this->city = $city;
        $this->url = $url;
        $this->country = $country;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStartDateTime(): DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
