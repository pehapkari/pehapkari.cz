<?php declare(strict_types=1);

namespace OpenTraining\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @docs https://symfony.com/doc/current/templating/twig_extension.html
 */
final class MenuExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return TwigFunction[]
     *
     * @see https://stackoverflow.com/a/48136670/1348344
     *
     * @use "<a href="..." class="{{ active_item('blog']) }}">"
     * @use "<a href="..." class="{{ active_item(['trainings', 'training-detail']) }}">"
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_item', function ($routes) {
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
