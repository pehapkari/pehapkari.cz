<?php declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use Pehapkari\NodeVisitor\ResolvedTemplateNameCollector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @docs https://symfony.com/doc/current/templating/twig_extension.html
 */
final class GithubEditUrlExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private const GITHUB_EDIT_PREFIX = 'https://github.com/pehapkari/pehapkari.cz/edit/master/templates/';

    /**
     * @var ResolvedTemplateNameCollector
     */
    private $resolvedTemplateNameCollector;

    public function __construct(ResolvedTemplateNameCollector $resolvedTemplateNameCollector)
    {
        $this->resolvedTemplateNameCollector = $resolvedTemplateNameCollector;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('github_edit_url', function (): string {
                $templateName = $this->resolvedTemplateNameCollector->getTemplateName();

                return self::GITHUB_EDIT_PREFIX . $templateName;
            }),
        ];
    }
}
