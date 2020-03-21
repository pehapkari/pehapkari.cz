<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Registration\Invoicing\Invoicer;
use Pehapkari\Registration\Repository\RegistrationRepository;

final class AdminRegistrationController extends EasyAdminController
{
    private RegistrationRepository $trainingRegistrationRepository;

    private Invoicer $invoicer;

    public function __construct(RegistrationRepository $trainingRegistrationRepository, Invoicer $invoicer)
    {
        $this->trainingRegistrationRepository = $trainingRegistrationRepository;
        $this->invoicer = $invoicer;
    }

    /**
     * @param int[] $ids
     */
    public function createInvoicesBatchAction(array $ids): void
    {
        $registrations = $this->trainingRegistrationRepository->findWithoutInvoicesByIds($ids);

        foreach ($registrations as $registration) {
            $this->invoicer->createInvoiceForRegistration($registration);

            $this->addFlash(
                'success',
                sprintf(
                    'Faktura pro %s %s byla vytvořena na Fakturoid.cz',
                    $registration->getTrainingName(),
                    $registration->getName()
                )
            );
        }
    }
}
