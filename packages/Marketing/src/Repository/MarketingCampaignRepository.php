<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Pehapkari\Marketing\Entity\MarketingCampaign;
use Pehapkari\Training\Entity\TrainingTerm;

final class MarketingCampaignRepository
{
    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(MarketingCampaign::class);
    }

    public function hasTrainingTermMarketingCampaign(TrainingTerm $trainingTerm): bool
    {
        return (bool) $this->entityRepository->findBy([
            'trainingTerm' => $trainingTerm,
        ]);
    }

    public function save(MarketingCampaign $marketingCampaign): void
    {
        $this->entityManager->persist($marketingCampaign);
        $this->entityManager->flush();
    }
}
