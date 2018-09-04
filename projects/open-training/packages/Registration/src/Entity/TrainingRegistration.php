<?php declare(strict_types=1);

namespace OpenTraining\Registration\Entity;

use App\Entity\TrainingTerm;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TrainingRegistration
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $ico;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TrainingTerm", inversedBy="registrations")
     * @var TrainingTerm
     */
    private $trainingTerm;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isPaid;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getIco(): ?string
    {
        return $this->ico;
    }

    public function setIco(string $ico): void
    {
        $this->ico = $ico;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function getTrainingTerm(): ?TrainingTerm
    {
        return $this->trainingTerm;
    }

    public function setTrainingTerm(TrainingTerm $trainingTerm): void
    {
        $this->trainingTerm = $trainingTerm;
    }

    public function isPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): void
    {
        $this->isPaid = $isPaid;
    }
}
