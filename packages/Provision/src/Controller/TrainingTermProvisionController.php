<?php declare(strict_types=1);

namespace Pehapkari\Provision\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Pehapkari\Provision\Repository\ExpenseRepository;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Mimics
 * @see https://docs.google.com/spreadsheets/d/1ubuko63ZEXPMzbOM87ouLYfUXTR1_yQcppxR7zsyZvA/edit#gid=1800966876
 */
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

        $profit = $trainingTerm->getIncome() - $trainingTermExpense->getTotal();

        // trainer
        $trainerProvisionRate = 50.0; // počítat pro daný termín!
        $trainerProvision = ceil($profit * ($trainerProvisionRate / 100.0)); // be nice with ceil :)
        $trainerProvisionWithExpense = $trainerProvision + $trainingTermExpense->getTrainerExpense();

        // organizer
        $organizerProvisionRate = (100.0 - $trainerProvisionRate) / 2;
        $organizerProvision = ceil($profit * ($organizerProvisionRate / 100)); // be nice with ceil :)
        $organizerProvisionWithExpense = $organizerProvision + $trainingTermExpense->getOrganizerExpense();

        // owner (the rest)
        $ownerProvisionRate = 100.0 - $trainerProvisionRate - $organizerProvisionRate;
        $ownerProvision = $profit - $trainerProvision - $organizerProvision;

        return $this->render('provision/training_term_provision.twig', [
            'trainer' => $trainingTerm->getTrainer(),
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
            // organizer
            'organizerProvisionRate' => $organizerProvisionRate,
            'organizerProvision' => $organizerProvision,
            'organizerProvisionWithExpense' => $organizerProvisionWithExpense,
            // owner
            'ownerProvisionRate' => $ownerProvisionRate,
            'ownerProvision' => $ownerProvision,
        ]);
    }
}
