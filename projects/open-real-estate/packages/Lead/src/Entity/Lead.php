<?php declare(strict_types=1);

namespace OpenRealEstate\Lead\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity
 */
class Lead
{
    use Timestampable;
    use Blameable;

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
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $comment;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $internalComment;

    /**
     * @ORM\ManyToOne(targetEntity="OpenRealEstate\Lead\Entity\LeadStatus")
     * @var LeadStatus
     */
    private $leadStatus;

    /**
     * @ORM\ManyToOne(targetEntity="OpenRealEstate\Lead\Entity\Adviser")
     * @var Adviser
     */
    private $adviser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getInternalComment(): ?string
    {
        return $this->internalComment;
    }

    public function setInternalComment(string $internalComment)
    {
        $this->internalComment = $internalComment;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getLeadStatus(): ?LeadStatus
    {
        return $this->leadStatus;
    }

    public function setLeadStatus(LeadStatus $leadStatus): void
    {
        $this->leadStatus = $leadStatus;
    }

    public function getAdviser(): ?Adviser
    {
        return $this->adviser;
    }

    public function setAdviser(Adviser $adviser): void
    {
        $this->adviser = $adviser;
    }
}
