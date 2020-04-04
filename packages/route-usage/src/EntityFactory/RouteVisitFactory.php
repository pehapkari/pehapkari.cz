<?php

declare(strict_types=1);

namespace Pehapkari\RouteUsage\EntityFactory;

use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Pehapkari\RouteUsage\Entity\RouteVisit;
use Symfony\Component\HttpFoundation\Request;

final class RouteVisitFactory
{
    public function createFromRequest(Request $request): RouteVisit
    {
        $routeParams = Json::encode($request->get('_route_params'));
        $createdAt = new DateTime();

        return new RouteVisit($request->get('_route'), $routeParams, $request->get('_controller'), $createdAt);
    }
}
