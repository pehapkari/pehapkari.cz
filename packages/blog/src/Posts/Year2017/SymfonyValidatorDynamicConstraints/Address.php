<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2017\SymfonyValidatorDynamicConstraints;

use Pehapkari\Blog\Posts\Year2017\SymfonyValidatorDynamicConstraints\Constraints\ZipCodeConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class Address
{
    /**
     * @Assert\NotBlank()
     * @Assert\Country()
     */
    private string $country;

    /**
     * @Assert\NotBlank()
     */
    private string $zipcode;

    /**
     * @Assert\Callback(groups = "zipcode")
     */
    public function validateZipcode(ExecutionContextInterface $executionContext): void
    {
        $constraint = new ZipCodeConstraint(['country' => $this->country]);
        $executionContext
            ->getValidator()
            ->inContext($executionContext)
            ->atPath('zipcode')
            ->validate($this->zipcode, $constraint, [Constraint::DEFAULT_GROUP]);
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }
}
