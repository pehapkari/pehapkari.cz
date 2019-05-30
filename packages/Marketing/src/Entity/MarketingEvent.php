<?php declare(strict_types=1);

namespace Pehapkari\Marketing\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MarketingEvent
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

    public function setId(?int $id): void
    {
        $this->id = $id;
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
}
