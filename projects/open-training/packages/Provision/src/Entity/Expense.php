<?php declare(strict_types=1);

namespace OpenTraining\Provision\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenTraining\Training\Entity\TrainingTerm;

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
     * @ORM\Column(type="text")
     * @var string
     */
    private $note;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $partner;

    /**
     * @ORM\ManyToOne(targetEntity="OpenTraining\Training\Entity\TrainingTerm")
     * @var TrainingTerm
     */
    private $trainingTerm;

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
