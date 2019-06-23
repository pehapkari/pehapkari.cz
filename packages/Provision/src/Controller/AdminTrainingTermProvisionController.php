<?php declare(strict_types=1);

namespace Pehapkari\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Provision\Repository\ExpenseRepository;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminTrainingTermProvisionController extends EasyAdminController
{
    /**
     * @var ExpenseRepository
     */
    private $expenseRepository;

    /**
     * @var TrainingTermRepository
     */
    private $trainingTermRepository;

    public function __construct(ExpenseRepository $expenseRepository, TrainingTermRepository $trainingTermRepository)
    {
        $this->expenseRepository = $expenseRepository;
        $this->trainingTermRepository = $trainingTermRepository;
    }

    /**
     * @Route(path="/admin/provision/{id}", name="training_term_provision")
     */
    public function trainingTermProvision(TrainingTerm $trainingTerm): Response
    {
        $trainingTermExpense = $this->expenseRepository->getExpensesByTrainingTerm($trainingTerm);

        $profit = $trainingTerm->getIncome() - $trainingTermExpense->getTotal();

        $previouslyFinishedTrainingCount = $this->trainingTermRepository->getCountOfPreviousTrainingTermsByTrainer(
            $trainingTerm
        );

        // trainer
        $trainerProvisionRate = $previouslyFinishedTrainingCount >= 5 ? 70.0 : 50.0;
        $trainerProvision = ceil($profit * ($trainerProvisionRate / 100.0)); // be nice with ceil :)
        $trainerProvisionWithExpense = $trainerProvision + $trainingTermExpense->getTrainerExpense();

        // owner (the rest)
        $ownerProvisionRate = 100.0 - $trainerProvisionRate;
        $ownerProvision = $profit - $trainerProvision;

        return $this->render('provision/training_term_provision.twig', [
            'trainer' => $trainingTerm->getTrainer(),
            'finished_training_count' => $previouslyFinishedTrainingCount,

            'training' => $trainingTerm->getTraining(),
            'trainingTerm' => $trainingTerm,
            // numbers
            'income' => $trainingTerm->getIncome(),
            'expense' => $trainingTermExpense->getTotal(),
            'profit' => $profit,
            // expense
            'ownerExpense' => $trainingTermExpense->getOwnerExpense(),
            'organizerExpense' => $trainingTermExpense->getOrganizerExpense(),
            'trainerExpense' => $trainingTermExpense->getTrainerExpense(),

            // trainer
            'trainerProvisionRate' => $trainerProvisionRate,
            'trainerProvision' => $trainerProvision,
            'trainerProvisionWithExpense' => $trainerProvisionWithExpense,

            // owner
            'ownerProvisionRate' => $ownerProvisionRate,
            'ownerProvision' => $ownerProvision,
        ]);
    }
}
