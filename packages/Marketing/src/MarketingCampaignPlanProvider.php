<?php declare(strict_types=1);

namespace Pehapkari\Marketing;

use Pehapkari\Marketing\ValueObject\MarketingCampaignPlanItem;

final class MarketingCampaignPlanProvider
{
    /**
     * @return MarketingCampaignPlanItem[]
     */
    public function provide(): array
    {
        $plan = [];

        $plan[] = new MarketingCampaignPlanItem(SocialPlatform::PLATFORM_FACEBOOK, 'training_feedback', 30);
        $plan[] = new MarketingCampaignPlanItem(SocialPlatform::PLATFORM_TWITTER, 'training_feedback', 30);

        return $plan;
    }
}
