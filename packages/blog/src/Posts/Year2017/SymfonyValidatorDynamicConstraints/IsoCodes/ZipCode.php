<?php

declare(strict_types=1);

namespace Pehapkari\Blog\Posts\Year2017\SymfonyValidatorDynamicConstraints\IsoCodes;

use Pehapkari\Blog\Posts\Year2017\SymfonyValidatorDynamicConstraints\Exception\MissingZipCodeValidatorException;

/**
 * Inspired by https://github.com/ronanguilloux/IsoCodes/blob/master/src/IsoCodes/ZipCode.php.
 */
final class ZipCode
{
    /**
     * @var string[]
     */
    private static array $patterns = [
        'CZ' => '\\d{3} ?\\d{2}',
        'US' => '(\\d{5})(?:[ \\-](\\d{4}))?',
    ];

    public static function isZipCodeValidWithCountry(string $zipcode, string $country): bool
    {
        $country = strtoupper($country);

        if (! isset(self::$patterns[$country])) {
            throw new MissingZipCodeValidatorException(sprintf(
                'The zipcode validator for "%s" does not exists yet: feel free to add it.',
                $country
            ));
        }

        return (bool) preg_match('/^(' . self::$patterns[$country] . ')$/', $zipcode);
    }

    /**
     * @return string[]
     */
    public static function getAvailableCountries(): array
    {
        return array_keys(self::$patterns);
    }
}
