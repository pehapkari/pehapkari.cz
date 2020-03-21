<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\ValueObject;

final class MarketingCampaignPlanItem
{
    private string $platform;

    private string $action;

    private int $daysInAdvance;

    public function __construct(string $platform, string $action, int $daysInAdvance)
    {
        $this->platform = $platform;
        $this->daysInAdvance = $daysInAdvance;
        $this->action = $action;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function getDaysInAdvance(): int
    {
        return $this->daysInAdvance;
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
