<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
final class MarketingEvent
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
     * @ORM\Column(type="datetime")
     * @var DateTimeInterface
     */
    private $plannedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Marketing\Entity\MarketingCampaign")
     * @var MarketingCampaign
     */
    private $marketingCampaign;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isDone = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id)
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getMarketingCampaign(): ?MarketingCampaign
    {
        return $this->marketingCampaign;
    }

    public function setMarketingCampaign(?MarketingCampaign $marketingCampaign): void
    {
        $this->marketingCampaign = $marketingCampaign;
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
}
