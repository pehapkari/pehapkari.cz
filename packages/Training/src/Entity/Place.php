<?php declare(strict_types=1);

namespace Pehapkari\Training\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Pehapkari\BetterEasyAdmin\Entity\UploadableImageTrait;
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
     * @ORM\Column(type="text")
     * @var string
     */
    private $perex;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $routeDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $googleMapUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $seznamMapUrl;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     * @var string
     */
    private $iframeUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $address;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isPublic = false;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Gedmo\Slug(fields={"name"})
     * @var string
     */
    private $slug;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getIframeUrl(): ?string
    {
        return $this->iframeUrl;
    }

    public function setIframeUrl(?string $iframeUrl): void
    {
        $this->iframeUrl = $iframeUrl;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerex(): ?string
    {
        return $this->perex;
    }

    public function setPerex(string $perex): void
    {
        $this->perex = $perex;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    public function getRouteDescription(): ?string
    {
        return $this->routeDescription;
    }

    public function setRouteDescription(?string $routeDescription): void
    {
        $this->routeDescription = $routeDescription;
    }

    public function getGoogleMapUrl(): ?string
    {
        return $this->googleMapUrl;
    }

    public function setGoogleMapUrl(?string $googleMapUrl): void
    {
        $this->googleMapUrl = $googleMapUrl;
    }

    public function getSeznamMapUrl(): ?string
    {
        return $this->seznamMapUrl;
    }

    public function setSeznamMapUrl(?string $seznamMapUrl): void
    {
        $this->seznamMapUrl = $seznamMapUrl;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }
}
