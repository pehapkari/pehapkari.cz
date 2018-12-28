<?php declare(strict_types=1);

namespace OpenTraining\Twig\Extension;

use OpenTraining\Exception\Twig\InvalidWordCountException;
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
     * @use "{{ word_by_count(training.getDuration(), ['hodin', 'hodiny', 'hodin']) }}" → 5 hodin
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('word_by_count', function (int $count, array $versions): string {
                $this->ensureValidFormCountIsProvided($versions);
                if ($count === 1) {
                    return $count . ' ' . $versions[0];
                }

                if ($count === 0 || $count < 5) {
                    return $count . ' ' . $versions[1];
                }

                return $count . ' ' . $versions[2];
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

        throw new InvalidWordCountException(sprintf(
            'Provide exactly 3 options to word_by_count() function as 2nd argument. %d given',
            count($versions)
        ));
    }
}
