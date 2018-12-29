<?php declare(strict_types=1);

namespace OpenTraining\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use OpenTraining\Provision\Data\Partner;
use OpenTraining\Provision\Entity\Expense;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * @see Expense
 */
final class ExpenseController extends EasyAdminController
{
    /**
     * @inheritdoc
     */
    protected function createEditForm($entity, array $entityProperties): FormInterface
    {
        $editForm = parent::createEditForm($entity, $entityProperties);
        $this->decorateForm($editForm);
        return $editForm;
    }

    /**
     * @inheritdoc
     */
    protected function createNewForm($entity, array $entityProperties): FormInterface
    {
        $newForm = parent::createNewForm($entity, $entityProperties);
        $this->decorateForm($newForm);
        return $newForm;
    }

    private function decorateForm(FormInterface $form): void
    {
        $form->add('partner', ChoiceType::class, [
            'choices' => [
                // label => value
                'Vlastník' => Partner::OWNER,
                'Organizátor' => Partner::ORGANIZER,
                'Školitel' => Partner::TRAINER,
            ],
        ]);
    }
}
