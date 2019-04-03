<?php declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use Symplify\Statie\Exception\Configuration\ConfigurationException;
use Symplify\Statie\Generator\RelatedItemsResolver;
use Symplify\Statie\Generator\Renderable\File\AbstractGeneratorFile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @see \Symplify\Statie\Generator\Latte\Filter\RelatedItemsFilterProvider
 * @see \Symplify\Statie\Templating\FilterProvider\GeneratorFilterProvider
 */
final class StatieFiltersExtension extends AbstractExtension
{
    /**
     * @var RelatedItemsResolver
     */
    private $relatedItemsResolver;

    public function __construct(RelatedItemsResolver $relatedItemsResolver)
    {
        $this->relatedItemsResolver = $relatedItemsResolver;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            // use: {% set relatedPosts = related_items(post) %}
            new TwigFilter(
                'related_items',
                function (AbstractGeneratorFile $generatorFile): array {
                    return $this->relatedItemsResolver->resolveForFile($generatorFile);
                }
            ),

            // use: {{ post|link }}
            new TwigFilter(
                'link',
                function ($generatorFile): string {
                    $this->ensureArgumentIsGeneratorFile($generatorFile);

                    /** @var AbstractGeneratorFile $generatorFile */
                    return $generatorFile->getRelativeUrl();
                }
            ),
        ];
    }

    /**
     * @param mixed $value
     */
    private function ensureArgumentIsGeneratorFile($value): void
    {
        if ($value instanceof AbstractGeneratorFile) {
            return;
        }

        $message = sprintf('Only "%s" can be passed to "%s" filter', AbstractGeneratorFile::class, 'link');

        if (is_object($value)) {
            $message .= ' ' . sprintf('"%s" given', get_class($value));
        } elseif (is_array($value)) {
            $message .= ' Array given';
        } elseif (is_numeric($value) || is_string($value)) {
            $message .= ' ' . sprintf('"%s" given', $value);
        }

        throw new ConfigurationException($message);
    }
}
