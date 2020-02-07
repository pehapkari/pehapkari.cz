<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\ValueObject;

final class MarketingCampaignPlanItem
{
    /**
     * @var string
     */
    private $platform;

    /**
     * @var string
     */
    private $action;

    /**
     * @var int
     */
    private $daysInAdvance;

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
