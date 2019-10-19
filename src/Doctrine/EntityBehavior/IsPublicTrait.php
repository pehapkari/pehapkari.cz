<?php

declare(strict_types=1);

namespace Pehapkari\Doctrine\EntityBehavior;

trait IsPublicTrait
{
    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isPublic = false;

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }
}
