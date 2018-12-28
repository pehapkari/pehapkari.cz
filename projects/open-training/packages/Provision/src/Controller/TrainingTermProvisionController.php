<?php declare(strict_types=1);

namespace OpenTraining\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use OpenTraining\Provision\Repository\ExpenseRepository;
use OpenTraining\Training\Entity\TrainingTerm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TrainingTermProvisionController extends EasyAdminController
{
    /**
     * @var ExpenseRepository
     */
    private $expenseRepository;

    public function __construct(ExpenseRepository $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    /**
     * @Route(path="/admin/provision/{id}", name="training_term_provision")
     */
    public function trainingTermProvision(TrainingTerm $trainingTerm): Response
    {
        $trainingTermExpense = $this->expenseRepository->getExpensesByTrainingTerm($trainingTerm);

        return $this->render('provision/training_term_provision.twig', [
            'trainer' => $trainingTerm->getTrainer(),
            'training' => $trainingTerm->getTraining(),
            'trainingTerm' => $trainingTerm,
            // numbers
            'income' => $trainingTerm->getIncome(),
            'expense' => $trainingTermExpense->getTotal(),
            'profit' => $trainingTerm->getIncome() - $trainingTermExpense->getTotal(),
            // expense
            'ownerExpense' => $trainingTermExpense->getOwnerExpense(),
            'organizerExpense' => $trainingTermExpense->getOrganizerExpense(),
            'trainerExpense' => $trainingTermExpense->getTrainerExpense(),
        ]);
    }
}
