<?php declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Pehapkari\Doctrine\EntityBehavior\IsPublicTrait;
use Pehapkari\Doctrine\EntityBehavior\IsRevisedTrait;

/**
 * @ORM\Entity
 */
class TrainingFeedback
{
    use Timestampable;
    use IsPublicTrait;
    use IsRevisedTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $pointOfEntry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $url;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    private $text;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var float|null
     */
    private $ratingContent;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @var float|null
     */
    private $ratingOrganization;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $thingsToImprove;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isAgreedWithPublishingName = false;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isShownOnMainPage = false;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\Training", inversedBy="trainingFeedbacks")
     * @var Training
     */
    private $training;

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

    public function getRatingContent(): ?float
    {
        return $this->ratingContent;
    }

    public function setRatingContent(?float $ratingContent): void
    {
        $this->ratingContent = $ratingContent;
    }

    public function getRatingOrganization(): ?float
    {
        return $this->ratingOrganization;
    }

    public function setRatingOrganization(float $ratingOrganization): void
    {
        $this->ratingOrganization = $ratingOrganization;
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
