<?php declare(strict_types=1);

namespace OpenTraining\Training\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenProject\BetterEasyAdmin\Entity\UploadableImageTrait;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Place
{
    use UploadableImageTrait;

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
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $mapUrl;

    public function __toString(): string
    {
        return $this->name;
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

    public function getMapUrl(): ?string
    {
        return $this->mapUrl;
    }

    public function setMapUrl(string $mapUrl): void
    {
        $this->mapUrl = $mapUrl;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
