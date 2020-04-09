<?php

declare(strict_types=1);

namespace Pehapkari\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Provision\ValueObject\Partner;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * @see \Pehapkari\Provision\Entity\Expense
 */
final class AdminExpenseController extends EasyAdminController
{
    private TrainingTermRepository $trainingTermRepository;

    public function __construct(TrainingTermRepository $trainingTermRepository)
    {
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @param object $entity
     * @param mixed[] $entityProperties
     */
    protected function createEditForm($entity, array $entityProperties): FormInterface
    {
        $editForm = parent::createEditForm($entity, $entityProperties);
        $this->addPartnerConstantChoices($editForm);

        return $editForm;
    }

    /**
     * @param object $entity
     * @param mixed[] $entityProperties
     */
    protected function createNewForm($entity, array $entityProperties): FormInterface
    {
        $newForm = parent::createNewForm($entity, $entityProperties);

        $this->selectTrainingTermBasedOnUrl($newForm);
        $this->addPartnerConstantChoices($newForm);

        return $newForm;
    }

    private function addPartnerConstantChoices(FormInterface $form): void
    {
        $form->add('partner', ChoiceType::class, [
            'choices' => [
                // label => value
                'Edukai, s. r. o.' => Partner::OWNER,
                'Trainer' => Partner::TRAINER,
            ],
        ]);
    }

    private function selectTrainingTermBasedOnUrl(FormInterface $form): void
    {
        $trainingTermId = $this->request->get('trainingTerm');
        if ($trainingTermId === null) {
            return;
        }

        $trainingTerm = $this->trainingTermRepository->getReference($trainingTermId);

        $expense = $form->getData();
        $expense->setTrainingTerm($trainingTerm);
    }
}
