<?php declare(strict_types=1);

namespace Pehapkari\Training\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Marketing\MarketingCampaignFactory;
use Pehapkari\Marketing\Repository\MarketingCampaignRepository;
use Pehapkari\Training\Repository\TrainingTermRepository;

/**
 * @see \Pehapkari\Training\Entity\TrainingTerm
 */
final class AdminTrainingTermController extends EasyAdminController
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    /**
     * @var MarketingCampaignRepository
     */
    private $marketingCampaignRepository;

    /**
     * @var MarketingCampaignFactory
     */
    private $marketingCampaignFactory;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        MarketingCampaignRepository $marketingCampaignRepository,
        MarketingCampaignFactory $marketingCampaignFactory
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->marketingCampaignRepository = $marketingCampaignRepository;
        $this->marketingCampaignFactory = $marketingCampaignFactory;
    }

    /**
     * @param int[] $ids
     */
    public function generateMarketingCampaignBatchAction(array $ids): void
    {
        $trainingTerms = $this->trainingTermRepository->findByIds($ids);

        foreach ($trainingTerms as $trainingTerm) {
            if ($this->marketingCampaignRepository->hasTrainingTermMarketingCampaign($trainingTerm)) {
                $this->addFlash('warning', sprintf('Kampaň pro termín "%s" už existuje', (string) $trainingTerm));
                continue;
            }

            $marketingCampaign = $this->marketingCampaignFactory->createMarketingCampaign($trainingTerm);

            $this->marketingCampaignRepository->save($marketingCampaign);

            $this->addFlash('success', sprintf('Kampaň pro "%s" byla vytvořena', (string) $trainingTerm));
        }
    }
}
