<?php declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Training
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Place")
     * @var Place
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trainer")
     * @var Trainer
     */
    private $trainer;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingTerm", mappedBy="training")
     * @var TrainingTerm[]|ArrayCollection
     */
    private $trainingTerms = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrainingReference", mappedBy="training")
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

    public function setDescription(string $description): void
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
}
