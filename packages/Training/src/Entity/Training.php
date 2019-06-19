<?php declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pehapkari\BetterEasyAdmin\Entity\UploadableImageTrait;
use Pehapkari\Doctrine\EntityBehavior\IsPublicTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Training
{
    use UploadableImageTrait;
    use IsPublicTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"})
     * @var string
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $certificateFormatedName;

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
    private $capacity;

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
     * @var TrainingTerm[]|ArrayCollection
     */
    private $trainingTerms = [];

    /**
     * @ORM\OneToMany(targetEntity="TrainingFeedback", mappedBy="training")
     * @var TrainingFeedback[]|ArrayCollection
     */
    private $trainingFeedbacks = [];

    public function __construct()
    {
        $this->trainingFeedbacks = new ArrayCollection();
        $this->trainingTerms = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
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

    public function getCapacity(): ?int
    {
        return $this->capacity;
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
     * @return TrainingTerm[]|ArrayCollection
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

    public function setCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
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

    public function getTrainerWebsite(): ?string
    {
        return $this->trainer->getWebsite();
    }

    public function getTrainerName(): ?string
    {
        return $this->trainer->getName();
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
        return (bool) $this->getFeedbackCount();
    }

    public function getFeedbackCount(): int
    {
        return count($this->trainingFeedbacks);
    }

    /**
     * @return TrainingFeedback[]|ArrayCollection
     */
    public function getPublicFeedbacks()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isPublic', true));

        return $this->trainingFeedbacks->matching($criteria);
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getCertificateFormatedName(): ?string
    {
        return $this->certificateFormatedName;
    }

    public function getNameForCertificate(): ?string
    {
        return $this->certificateFormatedName ?? $this->name;
    }

    public function setCertificateFormatedName(?string $certificateFormatedName): void
    {
        $this->certificateFormatedName = $certificateFormatedName;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return TrainingFeedback[]|ArrayCollection
     */
    public function getTrainingFeedbacks()
    {
        return $this->trainingFeedbacks;
    }
}
