<?php

declare(strict_types=1);

namespace Pehapkari\Provision;

use Pehapkari\Provision\ValueObject\ProvisionValueObject;
use Pehapkari\Training\Entity\TrainingTerm;
use Pehapkari\Training\Repository\TrainingTermRepository;

final class ProvisionResolver
{
    private TrainingTermRepository $trainingTermRepository;

    public function __construct(TrainingTermRepository $trainingTermRepository)
    {
        $this->trainingTermRepository = $trainingTermRepository;
    }

    public function resolveForTrainingTerm(TrainingTerm $trainingTerm): ProvisionValueObject
    {
        $previouslyFinishedTrainingCount = $this->trainingTermRepository->getCountOfPreviousTrainingTermsByTrainer(
            $trainingTerm
        );

        return new ProvisionValueObject(
            $previouslyFinishedTrainingCount,
            // numbers
            $trainingTerm->getIncome(),
            $trainingTerm->getExpensesTotal(),
            // expense
            $trainingTerm->getOwnerExpenseTotal(),
            $trainingTerm->getTrainerExpenseTotal()
        );
    }
}
