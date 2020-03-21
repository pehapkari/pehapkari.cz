<?php

declare(strict_types=1);

namespace Pehapkari\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @docs https://symfony.com/doc/current/templating/twig_extension.html
 */
final class MenuExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return TwigFunction[]
     *
     * @see https://stackoverflow.com/a/48136670/1348344
     *
     * Examples:
     * <a href="..." class="{{ active_item('blog']) }}">
     * <a href="..." class="{{ active_item(['trainings', 'training_detail']) }}">
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_item', function ($routes): string {
                if (! is_array($routes)) {
                    $routes = [$routes];
                }

                $currentRequest = $this->requestStack->getCurrentRequest();
                if ($currentRequest === null) {
                    return '';
                }

                $currentRoute = $currentRequest->get('_route');
                if (in_array($currentRoute, $routes, true)) {
                    return 'active';
                }

                return '';
            }),
        ];
    }
}
