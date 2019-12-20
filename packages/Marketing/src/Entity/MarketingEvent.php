<?php

declare(strict_types=1);

namespace Pehapkari\Marketing\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Pehapkari\Training\Entity\TrainingTerm;

/**
 * @ORM\Entity
 */
class MarketingEvent implements TimestampableInterface
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
    private $platform;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $action;

    /**
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private $plannedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTimeInterface|null
     */
    private $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\TrainingTerm", inversedBy="marketingEvents")
     * @var TrainingTerm
     */
    private $trainingTerm;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isDone = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function isDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(?bool $isDone): void
    {
        $this->isDone = $isDone;
    }

    public function getPlannedAt(): ?DateTimeInterface
    {
        return $this->plannedAt;
    }

    public function setPlannedAt(?DateTimeInterface $plannedAt): void
    {
        $this->plannedAt = $plannedAt;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setPlatform(?string $platform): void
    {
        $this->platform = $platform;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getPublishedAt(): ?DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getTrainingTerm(): ?TrainingTerm
    {
        return $this->trainingTerm;
    }

    public function setTrainingTerm(?TrainingTerm $trainingTerm): void
    {
        $this->trainingTerm = $trainingTerm;
    }
}
