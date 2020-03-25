<?php

declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Pehapkari\Doctrine\EntityBehavior\IsPublicTrait;
use Pehapkari\Doctrine\EntityBehavior\IsRevisedTrait;

/**
 * @ORM\Entity
 */
class TrainingFeedback implements TimestampableInterface
{
    use TimestampableTrait;
    use IsPublicTrait;
    use IsRevisedTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $pointOfEntry = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $url = null;

    /**
     * @ORM\Column(type="text")
     */
    private string $text;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $rating = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $thingsToImprove = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isAgreedWithPublishingName = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isShownOnMainPage = false;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\Training", inversedBy="trainingFeedbacks")
     */
    private ?Training $training = null;

    public function __toString(): string
    {
        return $this->text;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(Training $training): void
    {
        $this->training = $training;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getTrainingSlug(): string
    {
        return $this->training->getSlug();
    }

    public function getTrainingName(): string
    {
        return $this->training->getName();
    }

    public function getPointOfEntry(): ?string
    {
        return $this->pointOfEntry;
    }

    public function setPointOfEntry(?string $pointOfEntry): void
    {
        $this->pointOfEntry = $pointOfEntry;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): void
    {
        $this->rating = $rating;
    }

    public function getThingsToImprove(): ?string
    {
        return $this->thingsToImprove;
    }

    public function setThingsToImprove(string $thingsToImprove): void
    {
        $this->thingsToImprove = $thingsToImprove;
    }

    public function isAgreedWithPublishingName(): bool
    {
        return $this->isAgreedWithPublishingName;
    }

    public function setIsAgreedWithPublishingName(bool $isAgreedWithPublishingName): void
    {
        $this->isAgreedWithPublishingName = $isAgreedWithPublishingName;
    }

    public function isShownOnMainPage(): bool
    {
        return $this->isShownOnMainPage;
    }

    public function setIsShownOnMainPage(bool $isShownOnMainPage): void
    {
        $this->isShownOnMainPage = $isShownOnMainPage;
    }
}
