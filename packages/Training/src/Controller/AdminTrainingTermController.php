<?php declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Marketing\MarketingEventsFactory;
use Pehapkari\Marketing\Repository\MarketingEventRepository;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @var MarketingEventRepository
     */
    private $marketingEventRepository;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        MarketingEventRepository $marketingEventRepository,
        MarketingEventsFactory $marketingEventsFactory
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->marketingEventsFactory = $marketingEventsFactory;
        $this->marketingEventRepository = $marketingEventRepository;
    }

    /**
     * @Route(path="/admin/training-term-organization/{id}", name="training_term_organization")
     */
    public function trainingTermOrganization(TrainingTerm $trainingTerm): Response
    {
        return $this->render('training_term/organize.twig', [
            'trainingTerm' => $trainingTerm,
        ]);
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
            foreach ($marketingEvents as $marketingEvent) {
                $this->marketingEventRepository->save($marketingEvent);
            }

            $this->addFlash('success', sprintf('Kampaň pro "%s" byla vytvořena', (string) $trainingTerm));
        }
    }
}
