<?php declare(strict_types=1);

namespace OpenProject\BetterEasyAdmin\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

trait UploadableImageTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="image_uploads", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * This needs to be changed on file upload, so the image changes.
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime
     */
    private $imageUploadedAt;

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $file = null): void
    {
        $this->imageFile = $file;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($file) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->imageUploadedAt = new DateTime('now');
        }
    }

    public function getImageUploadedAt(): ?DateTime
    {
        return $this->imageUploadedAt;
    }

    public function setImageUploadedAt(DateTime $imageUploadedAt): void
    {
        $this->imageUploadedAt = $imageUploadedAt;
    }
}
