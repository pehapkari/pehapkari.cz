<?php

declare(strict_types=1);

namespace Pehapkari\RouteUsage\EventSubscriber;

use Nette\Utils\Strings;
use Pehapkari\RouteUsage\EntityFactory\RouteVisitFactory;
use Pehapkari\RouteUsage\EntityRepository\RouteVisitRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LogRouteUsageEventSubscriber implements EventSubscriberInterface
{
    private RouteVisitFactory $routeVisitFactory;

    private RouteVisitRepository $routeVisitRepository;

    public function __construct(RouteVisitRepository $routeVisitRepository, RouteVisitFactory $routeVisitFactory)
    {
        $this->routeVisitRepository = $routeVisitRepository;
        $this->routeVisitFactory = $routeVisitFactory;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => 'onController'];
    }

    public function onController(ControllerEvent $controllerEvent): void
    {
        $request = $controllerEvent->getRequest();
        if ($this->shouldSkipRequest($request)) {
            return;
        }

        $routeVisit = $this->routeVisitFactory->createFromRequest($request);
        $this->routeVisitRepository->save($routeVisit);
    }

    private function shouldSkipRequest(Request $request): bool
    {
        $route = $request->get('_route');

        // is probably some debug-route
        if (Strings::startsWith((string) $route, '_')) {
            return true;
        }

        if ($route === 'error_controller') {
            return true;
        }

        return $route === null;
    }
}
