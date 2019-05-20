<?php declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use Iterator;
use ParsedownExtra;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @docs https://symfony.com/doc/current/templating/twig_extension.html
 */
final class MarkdownExtension extends AbstractExtension
{
    /**
     * @var ParsedownExtra
     */
    private $parsedownExtra;

    public function __construct(ParsedownExtra $parsedownExtra)
    {
        $this->parsedownExtra = $parsedownExtra;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): Iterator
    {
        yield new TwigFilter('markdown', function (string $content): string {
            return $this->parsedownExtra->parse($content);
        });
    }
}
