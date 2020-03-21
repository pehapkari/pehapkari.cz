<?php

declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use Latte\Runtime\FilterExecutor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class LatteFiltersExtension extends AbstractExtension
{
    private FilterExecutor $filterExecutor;

    public function __construct(FilterExecutor $filterExecutor)
    {
        $this->filterExecutor = $filterExecutor;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        $twigFilters = [];
        foreach ($this->filterExecutor->getAll() as $name => $filter) {
            if ($this->shouldSkipAsDuplicatedInTwig($name)) {
                continue;
            }

            $twigFilters[] = new TwigFilter($name, $this->filterExecutor->{$filter});
        }

        return $twigFilters;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        $twigFunctions = [];
        foreach ($this->filterExecutor->getAll() as $name => $filter) {
            if ($this->shouldSkipAsDuplicatedInTwig($name)) {
                continue;
            }

            $twigFunctions[] = new TwigFunction($name, $this->filterExecutor->{$filter});
        }

        return $twigFunctions;
    }

    private function shouldSkipAsDuplicatedInTwig(string $name): bool
    {
        return in_array($name, ['replace', 'date'], true);
    }
}
