<?php declare(strict_types=1);

namespace OpenTraining\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use OpenTraining\Provision\Data\Partner;
use OpenTraining\Provision\Entity\Expense;
use OpenTraining\Training\Entity\TrainingTerm;
use OpenTraining\Training\Repository\TrainingTermRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * @see \OpenTraining\Provision\Entity\Expense
 */
final class ExpenseController extends EasyAdminController
{
    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(TrainingTermRepository $trainingTermRepository)
    {
        $this->trainingTermRepository = $trainingTermRepository;
    }

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

    protected function createExpenseEntityForm(): FormInterface
    {
        $expense = $this->createExpenseFromRequest();
        $formBuilder = $this->createEntityFormBuilder($expense, 'new');

        return $formBuilder->getForm();
    }

    private function decorateForm(FormInterface $form): void
    {
        $form->add('partner', ChoiceType::class, [
            'choices' => [
                // label => value
                'Organizátor' => Partner::ORGANIZER,
                'Školitel' => Partner::TRAINER,
                'Vlastník' => Partner::OWNER,
            ],
        ]);
    }

    private function createExpenseFromRequest(): Expense
    {
        $trainingTermId = $this->request->get('trainingTerm');
        $expense = new Expense();
        if ($trainingTermId) {
            $trainingTerm = $this->trainingTermRepository->getReference($trainingTermId);
            if ($trainingTerm instanceof TrainingTerm) {
                $expense->setTrainingTerm($trainingTerm);
            }
        }

        return $expense;
    }
}
