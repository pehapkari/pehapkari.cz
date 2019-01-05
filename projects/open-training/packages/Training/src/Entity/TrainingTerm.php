<?php declare(strict_types=1);

namespace OpenTraining\Training\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenTraining\Registration\Entity\TrainingRegistration;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 *
 * @see https://github.com/EasyCorp/EasyAdminBundle/issues/2566
 */
class TrainingTerm
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isProvisionPaid = false;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private $deadlineDateTime;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private $startDateTime;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private $endDateTime;

    /**
     * @ORM\ManyToOne(targetEntity="OpenTraining\Training\Entity\Training", inversedBy="trainingTerms")
     * @Assert\NotNull
     * @var Training
     */
    private $training;

    /**
     * @ORM\OneToMany(targetEntity="OpenTraining\Registration\Entity\TrainingRegistration", mappedBy="trainingTerm")
     * @var TrainingRegistration[]|Collection
     */
    private $registrations;

    /**
     * @ORM\ManyToOne(targetEntity="OpenTraining\Training\Entity\Place")
     * @var Place
     */
    private $place;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->training->getName() . ' - ' . $this->startDateTime->format('j. n. Y');
    }

    public function getTrainingName(): string
    {
        return (string) $this->training->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(Training $training): void
    {
        $this->training = $training;
    }

    public function isActive(): bool
    {
        return $this->startDateTime > new DateTime('now');
    }

    public function getStartDateTime(): ?DateTimeInterface
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(DateTimeInterface $startDateTime): void
    {
        $this->startDateTime = $startDateTime;
    }

    public function getEndDateTime(): ?DateTimeInterface
    {
        return $this->endDateTime;
    }

    public function setEndDateTime(DateTimeInterface $endDateTime): void
    {
        $this->endDateTime = $endDateTime;
    }

    public function getDeadlineDateTime(): ?DateTimeInterface
    {
        return $this->deadlineDateTime;
    }

    public function setDeadlineDateTime(DateTimeInterface $registrationDeadlineDateTime): void
    {
        $this->deadlineDateTime = $registrationDeadlineDateTime;
    }

    public function getStartDateTimeInFormat(string $format): string
    {
        return $this->startDateTime->format($format);
    }

    public function getEndDateTimeInFormat(string $format): string
    {
        return $this->endDateTime->format($format);
    }

    public function isProvisionPaid(): ?bool
    {
        return $this->isProvisionPaid;
    }

    public function setIsProvisionPaid(bool $isProvisionPaid): void
    {
        $this->isProvisionPaid = $isProvisionPaid;
    }

    public function getIncome(): float
    {
        $income = 0.0;

        foreach ($this->registrations as $registration) {
            if ($registration->isPaid()) {
                // @todo, price can change in time, registration should have own "price" unrelated to training price
                $income += $this->training->getPrice();
            }
        }

        return $income;
    }

    /**
     * @return TrainingRegistration[]|Collection
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getParticipantCount(): int
    {
        return count($this->registrations);
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateSlug(): void
    {
        $this->slug = $this->training->getSlug() . '-' . $this->startDateTime->format('Y-m-d');
    }

    public function getTrainer(): ?Trainer
    {
        return $this->training ? $this->training->getTrainer() : null;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): void
    {
        $this->place = $place;
    }
}
