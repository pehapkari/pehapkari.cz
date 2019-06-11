<?php declare(strict_types=1);

namespace Pehapkari\Marketing;

use Pehapkari\Marketing\Entity\MarketingCampaign;
use Pehapkari\Marketing\Entity\MarketingEvent;
use Pehapkari\Marketing\ValueObject\MarketingCampaignPlanItem;
use Pehapkari\Training\Entity\TrainingTerm;

final class MarketingCampaignFactory
{
    /**
     * @var MarketingCampaignPlanProvider
     */
    private $marketingCampaignPlanProvider;

    public function __construct(MarketingCampaignPlanProvider $marketingCampaignPlanProvider)
    {
        $this->marketingCampaignPlanProvider = $marketingCampaignPlanProvider;
    }

    public function createMarketingCampaign(TrainingTerm $trainingTerm): MarketingCampaign
    {
        $marketingCampaign = new MarketingCampaign();
        $marketingCampaign->setTrainingTerm($trainingTerm);

        foreach ($this->marketingCampaignPlanProvider->provide() as $marketingCampaignPlanItem) {
            $marketingCampaignEvent = $this->createMarketingEvent($trainingTerm, $marketingCampaignPlanItem);
            $marketingCampaign->addEvent($marketingCampaignEvent);
            $marketingCampaignEvent->setMarketingCampaign($marketingCampaign);
        }

        return $marketingCampaign;
    }

    private function createMarketingEvent(
        TrainingTerm $trainingTerm,
        MarketingCampaignPlanItem $marketingCampaignPlanItem
    ): MarketingEvent {
        $marketingEvent = new MarketingEvent();

        $plannedDateTime = clone $trainingTerm->getStartDateTime();
        $plannedDateTime->modify('- ' . $marketingCampaignPlanItem->getDaysInAdvance() . ' days');

        $marketingEvent->setPlannedAt($plannedDateTime);
        $marketingEvent->setPlatform($marketingCampaignPlanItem->getPlatform());
        $marketingEvent->setAction($marketingCampaignPlanItem->getAction());

        return $marketingEvent;
    }
}
