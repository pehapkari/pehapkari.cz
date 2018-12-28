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

    public function __construct(float $ownerExpense, float $organizerExpense, float $trainerExpense)
    {
        $this->ownerExpense = $ownerExpense;
        $this->organizerExpense = $organizerExpense;
        $this->trainerExpense = $trainerExpense;
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
