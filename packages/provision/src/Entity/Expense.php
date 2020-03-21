<?php

declare(strict_types=1);

namespace Pehapkari\Provision\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Expense
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private ?int $id;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount;

    /**
     * @ORM\Column(type="string")
     */
    private string $note;

    /**
     * @ORM\Column(type="string")
     */
    private string $partner;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\TrainingTerm", inversedBy="expenses")
     * @Assert\NotNull
     */
    private ?TrainingTerm $trainingTerm;

    public function __toString(): string
    {
        return (string) $this->amount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartner(): ?string
    {
        return $this->partner;
    }

    public function setPartner(?string $partner): void
    {
        $this->partner = $partner;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getTrainingTerm(): ?TrainingTerm
    {
        return $this->trainingTerm;
    }

    public function setTrainingTerm(TrainingTerm $trainingTerm): void
    {
        $this->trainingTerm = $trainingTerm;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }
}
