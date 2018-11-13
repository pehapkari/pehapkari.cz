<?php declare(strict_types=1);

namespace OpenProject\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

// @todo add custom edit method to set password!!!
// custom safe: https://symfony.com/doc/master/bundles/EasyAdminBundle/tutorials/custom-actions.html


/**
 * @ORM\Entity
 *
 * @see https://github.com/EliHood/symfonyormexample/blob/master/src/Entity/User.php#L13
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity="OpenProject\User\Entity\UserRole")
     * @var UserRole
     */
    private $userRole;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->name;
    }

    public function getRole(): ?UserRole
    {
        return $this->userRole;
    }

    public function setRole(UserRole $userRole): void
    {
        $this->userRole = $userRole;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Required by interface
     *
     * @return UserRole[]
     */
    public function getRoles(): array
    {
        return [$this->userRole];
    }
}
