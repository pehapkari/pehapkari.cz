<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Entity\TrainingTerm;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class TrainingRegistration implements TimestampableInterface
{
    use TimestampableTrait;

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
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $phone;

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
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(0)
     * @var int
     */
    private $participantCount;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $hasInvoice = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $agreesWithPersonalData = false;

    /**
     * @ORM\Column(type="float")
     * @var float
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $fakturoidInvoiceId;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\TrainingTerm", inversedBy="registrations")
     * @var TrainingTerm
     * @Assert\NotNull
     */
    private $trainingTerm;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

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

    public function getTrainingTermDate(): DateTimeInterface
    {
        return $this->trainingTerm->getStartDateTime();
    }

    public function getTrainingName(): string
    {
        return $this->trainingTerm->getTraining()->getName();
    }

    public function isAgreesWithPersonalData(): ?bool
    {
        return $this->agreesWithPersonalData;
    }

    public function setAgreesWithPersonalData(?bool $agreesWithPersonalData): void
    {
        $this->agreesWithPersonalData = $agreesWithPersonalData;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function hasInvoice(): ?bool
    {
        return $this->hasInvoice;
    }

    public function setHasInvoice(?bool $hasInvoice): void
    {
        $this->hasInvoice = $hasInvoice;
    }

    public function getParticipantCount(): ?int
    {
        return $this->participantCount;
    }

    public function setParticipantCount(?int $participantCount): void
    {
        $this->participantCount = $participantCount;
    }

    public function getFakturoidInvoiceId(): ?int
    {
        return $this->fakturoidInvoiceId;
    }

    public function setFakturoidInvoiceId(?int $fakturoidInvoiceId): void
    {
        $this->fakturoidInvoiceId = $fakturoidInvoiceId;
    }

    public function getTraining(): ?Training
    {
        return $this->trainingTerm->getTraining();
    }
}
