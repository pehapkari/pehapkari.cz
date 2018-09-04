<?php declare(strict_types=1);

namespace App\Request;

/**
 * @see https://blog.martinhujer.cz/symfony-forms-with-request-objects/
 */
final class ProvisionFormRequest
{
    /**
     * @var int
     */
    private $incomeAmount = 0;

    /**
     * @var int
     */
    private $lectorExpenses = 0;

    /**
     * @var int
     */
    private $organizerExpenses = 0;

    /**
     * @var int
     */
    private $ownerExpenses = 0;

    public function setIncomeAmount(int $incomeAmount): void
    {
        $this->incomeAmount = $incomeAmount;
    }

    public function getIncomeAmount(): int
    {
        return $this->incomeAmount;
    }

    public function getLectorExpenses(): int
    {
        return $this->lectorExpenses;
    }

    public function setLectorExpenses(int $lectorExpenses): void
    {
        $this->lectorExpenses = $lectorExpenses;
    }

    public function getOrganizerExpenses(): int
    {
        return $this->organizerExpenses;
    }

    public function setOrganizerExpenses(int $organizerExpenses): void
    {
        $this->organizerExpenses = $organizerExpenses;
    }

    public function getOwnerExpenses(): int
    {
        return $this->ownerExpenses;
    }

    public function setOwnerExpenses(int $ownerExpenses): void
    {
        $this->ownerExpenses = $ownerExpenses;
    }
}
