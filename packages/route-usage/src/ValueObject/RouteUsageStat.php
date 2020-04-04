<?php

declare(strict_types=1);

namespace Pehapkari\RouteUsage\ValueObject;

final class RouteUsageStat
{
    private string $route;
    private string $controller;
    private string $routeParams;
    private int $usageCount;

    public function __construct(string $route, string $controller, string $routeParams, int $usageCount)
    {
        $this->route = $route;
        $this->controller = $controller;

        $this->routeParams = $routeParams;
        $this->usageCount = $usageCount;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getRouteParams(): string
    {
        return $this->routeParams;
    }

    public function getUsageCount(): int
    {
        return $this->usageCount;
    }
}
