<?php declare(strict_types=1);

namespace OpenProject\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;
use OpenProject\User\Exception\InvalidEntityException;

/**
 * @ORM\Entity
 */
class UserRole
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $uid;

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

    public function setUid(string $uid): void
    {
        if (strtoupper($uid) !== $uid) {
            throw new InvalidEntityException(sprintf('Role uid "%s" needs to be uppercase', $uid));
        }

        if (! Strings::startsWith($uid, 'ROLE_')) {
            throw new InvalidEntityException(sprintf('Role uid "%s" needs start with "ROLE_"', $uid));
        }

        $this->uid = $uid;
    }

    public function getUid(): ?string
    {
        return $this->uid;
    }
}
