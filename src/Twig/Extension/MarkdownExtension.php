<?php

declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use ParsedownExtra;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @docs https://symfony.com/doc/current/templating/twig_extension.html
 */
final class MarkdownExtension extends AbstractExtension
{
    private ParsedownExtra $parsedownExtra;

    public function __construct(ParsedownExtra $parsedownExtra)
    {
        $this->parsedownExtra = $parsedownExtra;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        $twigFilter = new TwigFilter('markdown', fn (string $content): string => $this->parsedownExtra->parse(
            $content
        ));

        return [$twigFilter];
    }
}
