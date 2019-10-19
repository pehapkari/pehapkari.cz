<?php

declare(strict_types=1);

namespace Pehapkari\Training\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Marketing\MarketingEventsFactory;
use Pehapkari\Marketing\Repository\MarketingEventRepository;
use Pehapkari\Provision\ProvisionResolver;
use Pehapkari\Registration\Invoicing\Invoicer;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    /**
     * @var Invoicer
     */
    private $invoicer;

    /**
     * @var ProvisionResolver
     */
    private $provisionResolver;

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        MarketingEventRepository $marketingEventRepository,
        MarketingEventsFactory $marketingEventsFactory,
        Invoicer $invoicer,
        ProvisionResolver $provisionResolver
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->marketingEventsFactory = $marketingEventsFactory;
        $this->marketingEventRepository = $marketingEventRepository;
        $this->invoicer = $invoicer;
        $this->provisionResolver = $provisionResolver;
    }

    /**
     * @Route(path="/admin/training-term-organization/{id}", name="training_term_organization")
     */
    public function organize(TrainingTerm $trainingTerm): Response
    {
        return $this->render('training_term/organize.twig', [
            'trainingTerm' => $trainingTerm,
        ]);
    }

    /**
     * @Route(path="/admin/send-provision-term-email/{id}", name="training_term_provision_email")
     */
    public function provisionEmail(TrainingTerm $trainingTerm): Response
    {
        $provision = $this->provisionResolver->resolveForTrainingTerm($trainingTerm);

        // 1. send invoice email
        $trainerProvision = $provision->getTrainerProvision();

        // 2. send collected feedbacks
        $trainingTermFeedbacks = $trainingTerm->getFeedbacks();

        // @todo send email with good news to the trainer
        dump($trainerProvision, $trainingTermFeedbacks);
        die;
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

    /**
     * @Route(path="/admin/create-invoices-for-training-term/{id}", name="create_invoices_for_training_term")
     */
    public function createInvoicesForTrainingTerm(TrainingTerm $trainingTerm): RedirectResponse
    {
        foreach ($trainingTerm->getRegistrations() as $registration) {
            if ($registration->hasInvoice()) {
                continue;
            }

            $this->invoicer->createInvoiceForRegistration($registration);

            $flashMessage = sprintf(
                'Faktura pro "%s" "%s" byla vytvořena na Fakturoid.cz',
                $registration->getTrainingName(),
                $registration->getName()
            );

            $this->addFlash('success', $flashMessage);
        }

        return $this->redirectToRoute('easyadmin', [
            'action' => 'list',
            'entity' => $trainingTerm,
        ]);
    }
}
