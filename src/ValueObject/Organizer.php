<?php

declare(strict_types=1);

namespace Pehapkari\ValueObject;

final class Organizer
{
    private string $photo;

    private string $name;

    private string $responsibility;

    private ?string $url = null;

    private ?string $company = null;

    private ?string $companyUrl = null;

    public function __construct(
        string $name,
        string $photo,
        string $responsibility,
        ?string $url = null,
        ?string $company = null,
        ?string $companyUrl = null
    ) {
        $this->name = $name;
        $this->photo = $photo;
        $this->responsibility = $responsibility;
        $this->url = $url;
        $this->company = $company;
        $this->companyUrl = $companyUrl;
    }

    public function getCompanyUrl(): ?string
    {
        return $this->companyUrl;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getResponsibility(): string
    {
        return $this->responsibility;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }
}
