<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2017\SymfonyValidatorDynamicConstraints\Constraints;

use Pehapkari\Blog\Posts\Year2017\SymfonyValidatorDynamicConstraints\IsoCodes\ZipCode as IsoCodesZipCode;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

final class ZipCodeConstraint extends Constraint
{
    public ?string $country = null;

    public string $message = 'This value is not a valid ZIP code.';

    /**
     * @param mixed[] $options
     */
    public function __construct(?array $options = null)
    {
        parent::__construct($options);

        if (! in_array($this->country, IsoCodesZipCode::getAvailableCountries(), true)) {
            throw new ConstraintDefinitionException(sprintf(
                'The option "country" must be one of "%s" or "all"',
                implode('", "', IsoCodesZipCode::getAvailableCountries())
            ));
        }
    }
}
