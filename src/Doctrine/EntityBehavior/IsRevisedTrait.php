<?php

declare(strict_types=1);

namespace Pehapkari\Doctrine\EntityBehavior;

/**
 * This trait should make sure the input was checked and further processed.
 * E.g. new feedback, new anything.
 */
trait IsRevisedTrait
{
    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isRevised = false;

    public function isRevised(): bool
    {
        return $this->isRevised;
    }

    public function setIsRevised(bool $isRevised): void
    {
        $this->isRevised = $isRevised;
    }
}
