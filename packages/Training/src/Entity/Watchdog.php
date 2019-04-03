<?php declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * @ORM\Entity
 */
class Watchdog
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
     * @ORM\Column(type="text", length=255)
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     * @var string
     */
    private $note;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isInformed = false;

    /**
     * @ORM\ManyToOne(targetEntity="Pehapkari\Training\Entity\Training")
     * @var Training
     */
    private $training;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function isInformed(): ?bool
    {
        return $this->isInformed;
    }

    public function setIsInformed(?bool $isInformed): void
    {
        $this->isInformed = $isInformed;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): void
    {
        $this->note = $note;
    }

    public function getTraining(): ?Training
    {
        return $this->training;
    }

    public function setTraining(?Training $training): void
    {
        $this->training = $training;
    }
}
