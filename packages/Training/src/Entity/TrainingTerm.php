<?php declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime as NetteDateTime;
use Pehapkari\BetterEasyAdmin\Entity\UploadableImageTrait;
use Pehapkari\Contract\Doctrine\Entity\UploadDestinationAwareInterface;
use Pehapkari\Doctrine\EventSubscriber\SetUploadDestinationOnPostLoadEventSubscriber;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 *
 * @see https://github.com/EasyCorp/EasyAdminBundle/issues/2566
 */
class TrainingTerm implements UploadDestinationAwareInterface
{
    use UploadableImageTrait;

    /**
     * @var int
     */
    private const DEADLINE_DAYS_AHEAD = 7;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\Training", inversedBy="trainingTerms")
     * @Assert\NotNull
     * @var Training
     */
    private $training;

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
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $minParticipantCount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $maxParticipantCount;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTime
     */
    private $startDateTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $endDateTime;

    /**
     * @ORM\OneToMany(targetEntity="Pehapkari\Registration\Entity\TrainingRegistration", mappedBy="trainingTerm")
     * @var TrainingRegistration[]|Collection
     */
    private $registrations;

    /**
     * @var string
     */
    private $uploadDestination;

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

    public function getStartDateTime(): ?DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(DateTime $startDateTime): void
    {
        $this->startDateTime = $startDateTime;
    }

    public function getEndDateTime(): ?DateTime
    {
        return $this->endDateTime;
    }

    public function setEndDateTime(DateTime $endDateTime): void
    {
        $this->endDateTime = $endDateTime;
    }

    public function getDeadlineDateTime(): ?DateTime
    {
        if ($this->startDateTime === null) {
            return null;
        }

        $deadLineDateTime = clone $this->startDateTime;
        $deadLineDateTime->setTime(23, 59);

        return $deadLineDateTime->modify(sprintf('- %d days', self::DEADLINE_DAYS_AHEAD));
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
                $income += $registration->getPrice();
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

    public function getParticipationCount(): int
    {
        $count = 0;
        foreach ($this->registrations as $registration) {
            $count += $registration->getParticipantCount();
        }

        return $count;
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
        return $this->training->getTrainer();
    }

    public function getTrainerImageAbsolutePath(): ?string
    {
        return $this->getTrainerImage() ? $this->uploadDestination . $this->getTrainerImage() : null;
    }

    public function getTrainingImage(): ?string
    {
        return $this->training->getImage();
    }

    public function getTrainingImageAbsolutePath(): ?string
    {
        return $this->getTrainingImage() ? $this->uploadDestination . $this->getTrainingImage() : null;
    }

    public function getPrice(): ?float
    {
        if ($this->training === null) {
            return null;
        }

        return $this->training->getPrice();
    }

    public function getMinParticipantCount(): ?int
    {
        return $this->minParticipantCount;
    }

    public function setMinParticipantCount(?int $minParticipantCount): void
    {
        $this->minParticipantCount = $minParticipantCount;
    }

    public function getMaxParticipantCount(): ?int
    {
        return $this->maxParticipantCount;
    }

    public function setMaxParticipantCount(?int $maxParticipantCount): void
    {
        $this->maxParticipantCount = $maxParticipantCount;
    }

    /**
     * Parf of life cycle subscriber
     * @see SetUploadDestinationOnPostLoadEventSubscriber
     */
    public function setUploadDestination(string $uploadDestination): void
    {
        $this->uploadDestination = $uploadDestination;
    }

    public function getTrainingTermImageAbsolutePath(): ?string
    {
        return $this->getImage() ? $this->uploadDestination . $this->getImage() : null;
    }

    public function isRegistrationOpen(): bool
    {
        return $this->getDeadlineDateTime() > NetteDateTime::from('now');
    }

    private function getTrainerImage(): ?string
    {
        return $this->getTrainer() ? $this->getTrainer()->getImage() : null;
    }
}
