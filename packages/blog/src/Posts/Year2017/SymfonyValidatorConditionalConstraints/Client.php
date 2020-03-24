<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2017\SymfonyValidatorConditionalConstraints;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * @Assert\GroupSequenceProvider()
 */
final class Client implements GroupSequenceProviderInterface
{
    /**
     * @var int
     */
    public const TYPE_COMPANY = 1;

    /**
     * @var int
     */
    public const TYPE_PERSON = 2;

    /**
     * @Assert\NotNull()
     * @Assert\Choice({Client::TYPE_COMPANY, CLIENT::TYPE_PERSON})
     */
    private ?int $type = null;

    /**
     * @Assert\NotBlank(groups = {"company"})
     */
    private string $company;

    /**
     * @Assert\NotBlank(groups = {"person"})
     */
    private string $firstname;

    /**
     * @Assert\NotBlank(groups = {"person"})
     */
    private string $lastname;

    /**
     * @return string[][]
     */
    public function getGroupSequence(): array
    {
        return [['Client', $this->type === self::TYPE_PERSON ? 'person' : 'company']];
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }
}
