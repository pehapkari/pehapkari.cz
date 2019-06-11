<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Pehapkari\Training\Entity\TrainingTerm;

/**
 * @ORM\Entity
 */
class MarketingCampaign
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
     * @ORM\OneToOne(targetEntity="Pehapkari\Training\Entity\TrainingTerm")
     * @var TrainingTerm
     */
    private $trainingTerm;

    /**
     * @ORM\OneToMany(targetEntity="Pehapkari\Marketing\Entity\MarketingEvent", mappedBy="marketingCampaign", cascade={"persist"})
     * @var MarketingEvent[]
     */
    private $events = [];

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'Kampaň pro: ' . $this->trainingTerm;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrainingTerm(): ?TrainingTerm
    {
        return $this->trainingTerm;
    }

    public function setTrainingTerm(?TrainingTerm $trainingTerm): void
    {
        $this->trainingTerm = $trainingTerm;
    }

    /**
     * @return MarketingEvent[]|ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    public function addEvent(MarketingEvent $marketingEvent): void
    {
        $this->events->add($marketingEvent);
    }
}
