<?php declare(strict_types=1);

namespace OpenTraining\Provision\Data;

final class TrainingTermExpenses
{
    /**
     * @var float
     */
    private $ownerExpense;

    /**
     * @var float
     */
    private $organizerExpense;

    /**
     * @var float
     */
    private $trainerExpense;

    public function __construct(float $trainerExpense, float $organizerExpense, float $ownerExpense)
    {
        $this->trainerExpense = $trainerExpense;
        $this->organizerExpense = $organizerExpense;
        $this->ownerExpense = $ownerExpense;
    }

    public function getOwnerExpense(): float
    {
        return $this->ownerExpense;
    }

    public function getOrganizerExpense(): float
    {
        return $this->organizerExpense;
    }

    public function getTrainerExpense(): float
    {
        return $this->trainerExpense;
    }

    public function getTotal(): float
    {
        return $this->ownerExpense + $this->organizerExpense + $this->trainerExpense;
    }
}
