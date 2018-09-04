<?php declare(strict_types=1);

namespace OpenTraining\Provision\Entity;

use App\Entity\TrainingTerm;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
final class PartnerExpense
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="OpenTraining\Provision\Entity\Partner", inversedBy="expenses")
     * @var Partner
     */
    private $partner;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainingTerm")
     * @var TrainingTerm
     */
    private $trainingTerm;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(Partner $partner): void
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
}
