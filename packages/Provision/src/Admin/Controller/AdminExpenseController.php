<?php declare(strict_types=1);

namespace Pehapkari\Provision\Admin\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Provision\Data\Partner;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

/**
 * @see \Pehapkari\Provision\Entity\Expense
 */
final class AdminExpenseController extends EasyAdminController
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
        $this->addPartnerConstantChoices($editForm);
        return $editForm;
    }

    /**
     * @inheritdoc
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
                'Trainer' => Partner::TRAINER,
                'Edukai, s. r. o.' => Partner::OWNER,
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
