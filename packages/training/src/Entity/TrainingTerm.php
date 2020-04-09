<?php

declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\DateTime as NetteDateTime;
use Pehapkari\Marketing\Entity\MarketingEvent;
use Pehapkari\Provision\Entity\Expense;
use Pehapkari\Provision\ValueObject\Partner;
use Pehapkari\Registration\Entity\TrainingRegistration;
use Pehapkari\ValueObject\Place;
use Spatie\CalendarLinks\Link;
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
     * @var int
     */
    private const DEADLINE_DAYS_AHEAD = 7;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\Training", inversedBy="trainingTerms")
     * @Assert\NotNull
     */
    private ?Training $training = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private string $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isProvisionPaid = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isProvisionEmailSent = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $areFeedbackEmailsSent = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $startDateTime = null;

    /**
     * @ORM\OneToMany(targetEntity="Pehapkari\Registration\Entity\TrainingRegistration", mappedBy="trainingTerm")
     * @var Collection&TrainingRegistration[]
     */
    private Collection $registrations;

    /**
     * @ORM\OneToMany(targetEntity="Pehapkari\Marketing\Entity\MarketingEvent", cascade={"persist", "remove"}, mappedBy="trainingTerm")
     * @var Collection&MarketingEvent[]
     */
    private Collection $marketingEvents;

    /**
     * @ORM\OneToMany(targetEntity="Pehapkari\Provision\Entity\Expense", cascade={"remove"}, mappedBy="trainingTerm")
     * @var Collection&Expense[]
     */
    private Collection $expenses;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->marketingEvents = new ArrayCollection();
        $this->expenses = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->training->getName() . ' - ' . $this->startDateTime->format('j. n. Y');
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
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
        if ($this->training->getDuration() === null) {
            return null;
        }

        if ($this->startDateTime === null) {
            return null;
        }

        $endDateTime = clone $this->startDateTime;
        $endDateTime->modify('+' . $this->training->getDuration() . ' hours');

        return $endDateTime;
    }

    public function getDeadlineDateTime(): ?DateTimeInterface
    {
        if ($this->startDateTime === null) {
            return null;
        }

        $deadLineDateTime = clone $this->startDateTime;
        $deadLineDateTime->setTime(23, 59);

        return $deadLineDateTime->modify(sprintf('- %d days', self::DEADLINE_DAYS_AHEAD));
    }

    public function isProvisionPaid(): ?bool
    {
        return $this->isProvisionPaid;
    }

    public function setIsProvisionPaid(bool $isProvisionPaid): void
    {
        $this->isProvisionPaid = $isProvisionPaid;
    }

    public function isProvisionEmailSent(): bool
    {
        return $this->isProvisionEmailSent;
    }

    public function setIsProvisionEmailSent(bool $isProvisionEmailSent): void
    {
        $this->isProvisionEmailSent = $isProvisionEmailSent;
    }

    public function getIncome(): float
    {
        $income = 0.0;

        foreach ($this->registrations as $registration) {
            $income += $registration->getPrice() * $registration->getParticipantCount();
        }

        return $income;
    }

    /**
     * @return TrainingRegistration[]&Collection
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function getFromToHumanReadable(): string
    {
        return $this->startDateTime->format('j. n., H:i')
            . ' - '
            . $this->getEndDateTime()->format('H:i');
    }

    public function getGoogleCalendarLink(): string
    {
        return $this->getCalendarLink()->google();
    }

    public function getIcalCalendarLink(): string
    {
        return $this->getCalendarLink()->ics();
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
        $count = 0;
        foreach ($this->registrations as $registration) {
            $count += $registration->getParticipantCount();
        }

        return $count;
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

    public function getPrice(): ?float
    {
        if ($this->training === null) {
            return null;
        }

        return $this->training->getPrice();
    }

    public function setAreFeedbackEmailsSent(bool $areFeedbackEmailsSent): void
    {
        $this->areFeedbackEmailsSent = $areFeedbackEmailsSent;
    }

    public function areFeedbackEmailsSent(): bool
    {
        return $this->areFeedbackEmailsSent;
    }

    public function isRegistrationOpened(): bool
    {
        return $this->getDeadlineDateTime() > NetteDateTime::from('now');
    }

    public function hasMarketingEvents(): bool
    {
        return (bool) $this->marketingEvents;
    }

    /**
     * @return Collection&Expense[]
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    /**
     * @return Collection&MarketingEvent[]
     */
    public function getMarketingEvents(): Collection
    {
        return $this->marketingEvents;
    }

    public function hasMissingInvoices(): bool
    {
        foreach ($this->getRegistrations() as $registration) {
            if ($registration->hasInvoice() === false) {
                return true;
            }
        }

        return false;
    }

    public function getOwnerExpenseTotal(): float
    {
        return $this->getExpenseTotalByPartner(Partner::OWNER);
    }

    public function getTrainerExpenseTotal(): float
    {
        return $this->getExpenseTotalByPartner(Partner::TRAINER);
    }

    public function getExpensesTotal(): float
    {
        $amount = 0.0;
        foreach ($this->expenses as $expense) {
            $amount += $expense->getAmount();
        }

        return $amount;
    }

    /**
     * @return TrainingFeedback[]&Collection
     */
    public function getFeedbacks(): Collection
    {
        $trainingFeedbacks = $this->training->getFeedbacks();

        $startDateTime = $this->getStartDateTime();
        $monthAfterStartDateTime = clone $startDateTime;
        $monthAfterStartDateTime->modify('+ 1 month');

        // we have to limit all feedback to just those for this term
        return $trainingFeedbacks->filter(
            function (TrainingFeedback $trainingFeedback) use ($startDateTime, $monthAfterStartDateTime) {
                // is way old
                if ($trainingFeedback->getCreatedAt() < $startDateTime) {
                    return false;
                }

                // is way new
                if ($trainingFeedback->getCreatedAt() > $monthAfterStartDateTime) {
                    return false;
                }

                // feedback was given in a month after training
                return true;
            }
        );
    }

    /**
     * @see https://github.com/spatie/calendar-links
     */
    private function getCalendarLink(): Link
    {
        $link = new Link('Školení ' . $this->training->getName(), $this->getStartDateTime(), $this->getEndDateTime());
        // no better way to do this
        $absoluteUrl = 'https://pehapkari.cz/kurz/' . $this->training->getSlug();
        $link->description($absoluteUrl);
        // change to entity when 2+ more places
        $link->address(Place::PRAGUE_ADDRESS);

        return $link;
    }

    private function getExpenseTotalByPartner(string $partnerKind): float
    {
        $amount = 0.0;
        foreach ($this->expenses as $expense) {
            if ($expense->getPartner() !== $partnerKind) {
                continue;
            }

            $amount += $expense->getAmount();
        }

        return $amount;
    }
}
