<?php declare(strict_types=1);

namespace OpenTraining\Training\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Training
{
    use Timestampable;

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
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $image;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $perex;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $description;

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
     * @Vich\UploadableField(mapping="training_images", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\ManyToOne(targetEntity="OpenTraining\Training\Entity\Place")
     * @var Place
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="OpenTraining\Training\Entity\Trainer")
     * @var Trainer
     */
    private $trainer;

    /**
     * @ORM\OneToMany(targetEntity="OpenTraining\Training\Entity\TrainingTerm", mappedBy="training")
     * @var TrainingTerm[]|ArrayCollection
     */
    private $trainingTerms = [];

    /**
     * @ORM\OneToMany(targetEntity="OpenTraining\Training\Entity\TrainingReference", mappedBy="training")
     * @var TrainingReference[]|ArrayCollection
     */
    private $trainingReferences = [];

    public function __construct()
    {
        $this->trainingReferences = new ArrayCollection();
        $this->trainingTerms = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getNearestTerm(): ?TrainingTerm
    {
        foreach ($this->trainingTerms as $trainingTerm) {
            if ($trainingTerm->isActive()) {
                return $trainingTerm;
            }
        }

        return null;
    }

    public function getNearestTermDeadline(): ?DateTimeInterface
    {
        return $this->getNearestTerm() ? $this->getNearestTerm()->getDeadlineDateTime() : null;
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

    public function getPlace(): ?Place
    {
        return $this->place;
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

    public function setPlace(Place $place): void
    {
        $this->place = $place;
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

    public function hasReferences(): bool
    {
        return (bool) count($this->trainingReferences);
    }

    /**
     * @return TrainingReference[]|ArrayCollection
     */
    public function getReferences()
    {
        return $this->trainingReferences;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getNearestTermSlug(): ?string
    {
        if (! $this->getNearestTerm()) {
            return null;
        }

        return $this->getNearestTerm()->getSlug();
    }

    public function setImageFile(?File $file = null): void
    {
        $this->imageFile = $file;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($file) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new DateTime('now');
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
