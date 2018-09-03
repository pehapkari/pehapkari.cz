<?php declare(strict_types=1);

namespace OpenRealEstate\Lead\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity
 */
class Lead
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
     * @ORM\ManyToOne(targetEntity="OpenRealEstate\Lead\Entity\LeadStatus")
     * @var LeadStatus
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="OpenRealEstate\Lead\Entity\Adviser")
     * @var Adviser
     */
    private $adviser;

    /**
     * "Zadal"
     * @todo ask ? string or the current user?
     */
    //private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
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

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    public function getStatus(): ?LeadStatus
    {
        return $this->status;
    }

    public function setStatus(LeadStatus $status)
    {
        $this->status = $status;
    }

    public function getAdviser(): ?Adviser
    {
        return $this->adviser;
    }

    public function setAdviser(Adviser $adviser)
    {
        $this->adviser = $adviser;
    }
}
