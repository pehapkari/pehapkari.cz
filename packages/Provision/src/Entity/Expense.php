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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $amount;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $note;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $partner;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\TrainingTerm", inversedBy="expenses")
     * @var TrainingTerm
     * @Assert\NotNull
     */
    private $trainingTerm;

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
