<?php

declare(strict_types=1);

namespace Pehapkari\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminTrainingTermProvisionController extends EasyAdminController
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
     * @Route(path="/admin/provision/{id}", name="training_term_provision")
     */
    public function trainingTermProvision(TrainingTerm $trainingTerm): Response
    {
        $profit = $trainingTerm->getIncome() - $trainingTerm->getExpensesTotal();

        $previouslyFinishedTrainingCount = $this->trainingTermRepository->getCountOfPreviousTrainingTermsByTrainer(
            $trainingTerm
        );

        // trainer
        $trainerProvisionRate = $previouslyFinishedTrainingCount >= 5 ? 70.0 : 50.0;
        $trainerProvision = ceil($profit * ($trainerProvisionRate / 100.0)); // be nice with ceil :)
        $trainerProvisionWithExpense = $trainerProvision + $trainingTerm->getTrainerExpenseTotal();

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
            'expenses_total' => $trainingTerm->getExpensesTotal(),
            'profit' => $profit,

            // expense
            'owner_expense_total' => $trainingTerm->getOwnerExpenseTotal(),
            'trainer_expense_total' => $trainingTerm->getTrainerExpenseTotal(),

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
