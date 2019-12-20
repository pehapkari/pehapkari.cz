<?php

declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use Nette\Utils\Strings;
use Pehapkari\Exception\ShouldNotHappenException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @docs https://symfony.com/doc/current/templating/twig_extension.html
 *
 * Plural, singular,
 */
final class InflectionExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     *
     * Examples:
     * {{ word_by_count(training.getDuration(), ['hodin', 'hodiny', 'hodin']) }}
     * ↓
     * 5 hodin
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('word_by_count', function (int $count, array $versions): string {
                $this->ensureValidFormCountIsProvided($versions);

                $form = $this->resolveForm($count, $versions);
                if (Strings::contains($form, '%d')) {
                    return sprintf($form, $count);
                }

                return $count . ' ' . $form;
            }),
        ];
    }

    /**
     * @param string[] $versions
     */
    private function ensureValidFormCountIsProvided(array $versions): void
    {
        if (count($versions) === 3) {
            return;
        }

        throw new ShouldNotHappenException(sprintf(
            'Provide exactly 3 options to word_by_count() function as 2nd argument. %d given',
            count($versions)
        ));
    }

    /**
     * @param string[] $versions
     */
    private function resolveForm(int $count, array $versions): string
    {
        if ($count === 1) {
            return $versions[0];
        }

        if ($count === 0 || $count < 5) {
            return $versions[1];
        }

        return $versions[2];
    }
}
