<?php declare(strict_types=1);

namespace Pehapkari\Training\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Marketing\MarketingEventsFactory;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
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

    public function __construct(
        TrainingTermRepository $trainingTermRepository,
        MarketingEventsFactory $marketingEventsFactory
    ) {
        $this->trainingTermRepository = $trainingTermRepository;
        $this->marketingEventsFactory = $marketingEventsFactory;
    }

    /**
     * @Route(path="/admin/provision/send-email-to-trainer-with-amount-to-invoice/{id}", name="send_email_to_trainer_with_amount_to_invoice")
     */
    public function sendEmailToTrainerWithAmountToInvoice(TrainingTerm $trainingTerm)
    {
        $trainerEmail = $trainingTerm->getTrainer()->getEmail();
        $this->ensureTrainerEmailIsSet($trainingTerm, $trainerEmail);

        dump($trainerEmail);
        dump($trainingTerm);
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
            $trainingTerm->setMarketingEvents($marketingEvents);

            $this->trainingTermRepository->save($trainingTerm);

            $this->addFlash('success', sprintf('Kampaň pro "%s" byla vytvořena', (string) $trainingTerm));
        }
    }

    private function ensureTrainerEmailIsSet(TrainingTerm $trainingTerm, ?string $trainerEmail): void
    {
        if ($trainerEmail !== null) {
            return;
        }

        throw new ShouldNotHappenException(sprintf(
            'Cannot send email to "%s" for "%s" training. He or she have no email :(',
            $trainingTerm->getTrainer()->getName(),
            (string)$trainingTerm
        ));
    }
}
