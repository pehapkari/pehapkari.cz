<?php declare(strict_types=1);

namespace Pehapkari\Marketing;

use Pehapkari\Marketing\ValueObject\MarketingCampaignPlanItem;

final class MarketingCampaignPlanProvider
{
    /**
     * @var string
     */
    private const PLATFORM_FACEBOOK = 'facebook';

    /**
     * @var string
     */
    private const PLATFORM_TWITTER = 'twitter';

    /**
     * @return MarketingCampaignPlanItem[]
     */
    public function provide(): array
    {
        $plan = [];

        $plan[] = new MarketingCampaignPlanItem(self::PLATFORM_FACEBOOK, 'training_feedback', 30);
        $plan[] = new MarketingCampaignPlanItem(self::PLATFORM_TWITTER, 'training_feedback', 30);

        return $plan;
    }
}
