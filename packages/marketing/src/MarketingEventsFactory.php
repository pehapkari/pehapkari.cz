<?php

declare(strict_types=1);

namespace Pehapkari\Marketing;

use DateTime;
use Pehapkari\Marketing\Entity\MarketingEvent;
use Pehapkari\Marketing\ValueObject\MarketingCampaignPlanItem;
use Pehapkari\Training\Entity\TrainingTerm;

final class MarketingEventsFactory
{
    private MarketingCampaignPlanProvider $marketingCampaignPlanProvider;

    public function __construct(MarketingCampaignPlanProvider $marketingCampaignPlanProvider)
    {
        $this->marketingCampaignPlanProvider = $marketingCampaignPlanProvider;
    }

    /**
     * @return MarketingEvent[]
     */
    public function createMarketingEvents(TrainingTerm $trainingTerm): array
    {
        $marketingCampaignEvents = [];

        foreach ($this->marketingCampaignPlanProvider->provide() as $marketingCampaignPlanItem) {
            $marketingCampaignEvent = $this->createMarketingEvent($trainingTerm, $marketingCampaignPlanItem);
            $marketingCampaignEvent->setTrainingTerm($trainingTerm);
            $marketingCampaignEvents[] = $marketingCampaignEvent;
        }

        return $marketingCampaignEvents;
    }

    private function createMarketingEvent(
        TrainingTerm $trainingTerm,
        MarketingCampaignPlanItem $marketingCampaignPlanItem
    ): MarketingEvent {
        $marketingEvent = new MarketingEvent();

        /** @var DateTime $plannedDateTime */
        $plannedDateTime = clone $trainingTerm->getStartDateTime();
        $plannedDateTime->modify('- ' . $marketingCampaignPlanItem->getDaysInAdvance() . ' days');

        $marketingEvent->setPlannedAt($plannedDateTime);
        $marketingEvent->setPlatform($marketingCampaignPlanItem->getPlatform());
        $marketingEvent->setAction($marketingCampaignPlanItem->getAction());

        return $marketingEvent;
    }
}
