<?php

declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Pehapkari\BetterEasyAdmin\Entity\UploadableImageTrait;
use Pehapkari\Contract\Doctrine\Entity\UploadDestinationAwareInterface;
use Pehapkari\Doctrine\EntityBehavior\IsPublicTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Training implements UploadDestinationAwareInterface, SluggableInterface
{
    use UploadableImageTrait;
    use IsPublicTrait;
    use SluggableTrait;

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
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $certificateFormattedName;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $perex;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $hashtags;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\Trainer", inversedBy="trainings")
     * @Assert\NotNull()
     * @var Trainer
     */
    private $trainer;

    /**
     * @ORM\OneToMany(targetEntity="Pehapkari\Training\Entity\TrainingTerm", mappedBy="training")
     * @var TrainingTerm[]|Collection
     */
    private $trainingTerms = [];

    /**
     * @ORM\OneToMany(targetEntity="TrainingFeedback", mappedBy="training")
     * @var TrainingFeedback[]|Collection
     */
    private $trainingFeedbacks = [];

    /**
     * @var string
     */
    private $uploadDestination;

    public function __construct()
    {
        $this->trainingFeedbacks = new ArrayCollection();
        $this->trainingTerms = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->trainer->getName() . ' ' . $this->name;
    }

    public function getHashtags(): ?string
    {
        return $this->hashtags;
    }

    public function setHashtags(?string $hashtags): void
    {
        $this->hashtags = $hashtags;
    }

    public function getNearestTerm(): ?TrainingTerm
    {
        // @todo sort by datetime - what if there are 2 past active terms? or 2 future?
        foreach ($this->trainingTerms as $trainingTerm) {
            if ($trainingTerm->isActive()) {
                return $trainingTerm;
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getTrainer(): ?Trainer
    {
        return $this->trainer;
    }

    public function isActive(): ?bool
    {
        foreach ($this->trainingTerms as $trainingTerm) {
            if ($trainingTerm->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return TrainingTerm[]|Collection
     */
    public function getTrainingTerms(): iterable
    {
        return $this->trainingTerms;
    }

    public function setTrainer(Trainer $trainer): void
    {
        $this->trainer = $trainer;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getPerex(): ?string
    {
        return $this->perex;
    }

    public function setPerex(string $perex): void
    {
        $this->perex = $perex;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function hasFeedbacks(): bool
    {
        return (bool) count($this->trainingFeedbacks);
    }

    public function getParticipantCount(): int
    {
        $participantCount = 0;

        foreach ($this->trainingTerms as $trainingTerm) {
            $participantCount += $trainingTerm->getParticipantCount();
        }

        return $participantCount;
    }

    /**
     * @return TrainingFeedback[]|Collection
     */
    public function getPublicFeedbacks()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isPublic', true));

        return $this->trainingFeedbacks->matching($criteria);
    }

    /**
     * @return TrainingFeedback[]|Collection
     */
    public function getFeedbacks()
    {
        return $this->trainingFeedbacks;
    }

    public function getCertificateFormattedName(): ?string
    {
        return $this->certificateFormattedName;
    }

    public function getNameForCertificate(): ?string
    {
        return $this->certificateFormattedName ?? $this->name;
    }

    public function setCertificateFormattedName(?string $certificateFormattedName): void
    {
        $this->certificateFormattedName = $certificateFormattedName;
    }

    public function setUploadDestination(string $uploadDestination): void
    {
        $this->uploadDestination = $uploadDestination;
    }

    public function getImageAbsolutePath(): ?string
    {
        return $this->getImage() ? $this->uploadDestination . $this->getImage() : null;
    }

    public function getAverageRating(): ?float
    {
        /** @var TrainingFeedback[]|Collection $trainingFeedbacksWithRating */
        $trainingFeedbacksWithRating = $this->trainingFeedbacks->filter(function (TrainingFeedback $trainingFeedback) {
            return $trainingFeedback->getRating() !== null;
        });

        // no rating yet
        if ($trainingFeedbacksWithRating->count() === 0) {
            return null;
        }

        $absoluteRating = 0;
        foreach ($trainingFeedbacksWithRating as $trainingFeedbackWithRating) {
            $absoluteRating += $trainingFeedbackWithRating->getRating();
        }

        $averageRating = $absoluteRating / $trainingFeedbacksWithRating->count();

        return round($averageRating, 2);
    }

    public function getAverageRatingStarCount(): ?int
    {
        $averageRating = $this->getAverageRating();
        if ($averageRating === null) {
            return null;
        }

        return (int) round($averageRating, 0);
    }

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['name'];
    }
}
