<?php declare(strict_types=1);

namespace Pehapkari\Training\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Marketing\MarketingEventsFactory;
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
     * @var MarketingEventsFactory
     */
    private $marketingEventsFactory;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        MarketingEventsFactory $marketingEventsFactory
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->marketingEventsFactory = $marketingEventsFactory;
    }

    /**
     * @param int[] $ids
     */
    public function generateMarketingEventsBatchAction(array $ids): void
    {
        $trainingTerms = $this->trainingTermRepository->findByIds($ids);

        foreach ($trainingTerms as $trainingTerm) {
            if ($trainingTerm->hasMarketingEvents()) {
                $this->addFlash('warning', sprintf('Kampaň pro termín "%s" už existuje', (string) $trainingTerm));
                continue;
            }

            $marketingEvents = $this->marketingEventsFactory->createMarketingEvents($trainingTerm);
            $trainingTerm->setMarketingEvents($marketingEvents);

            $this->trainingTermRepository->save($trainingTerm);

            $this->addFlash('success', sprintf('Kampaň pro "%s" byla vytvořena', (string) $trainingTerm));
        }
    }
}
