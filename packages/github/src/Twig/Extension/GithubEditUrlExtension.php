<?php

declare(strict_types=1);

namespace Pehapkari\Github\Twig\Extension;

use Nette\Utils\Strings;
use Pehapkari\Github\Collector\ResolvedTemplateNameCollector;
use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
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

    private ResolvedTemplateNameCollector $resolvedTemplateNameCollector;

    private FinderSanitizer $finderSanitizer;

    public function __construct(
        ResolvedTemplateNameCollector $resolvedTemplateNameCollector,
        FinderSanitizer $finderSanitizer
    ) {
        $this->resolvedTemplateNameCollector = $resolvedTemplateNameCollector;
        $this->finderSanitizer = $finderSanitizer;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        $githubEditUrlFunction = new TwigFunction('github_edit_url', function (): ?string {
            $templateName = $this->resolveTemplateFromCurrentController();
            if ($templateName === null) {
                return null;
            }

            return self::GITHUB_EDIT_PREFIX . $templateName;
        });

        return [$githubEditUrlFunction];
    }

    private function resolveTemplateFromCurrentController(): ?string
    {
        $templateName = $this->resolvedTemplateNameCollector->getTemplateName();
        if ($templateName === null) {
            return null;
        }

        // all template finder
        $fileInfos = $this->findTwigFiles();

        foreach ($fileInfos as $fileInfo) {
            if (Strings::endsWith($fileInfo->getRelativeFilePath(), $templateName)) {
                return $fileInfo->getRelativeFilePathFromCwd();
            }
        }

        return $templateName;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function findTwigFiles(): array
    {
        $finder = new Finder();

        $finder->files()->name('*.twig')
            ->in(__DIR__ . '/../../../../../packages')
            ->in(__DIR__ . '/../../../../../templates');

        return $this->finderSanitizer->sanitize($finder);
    }
}
