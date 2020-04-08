<?php

declare(strict_types=1);

namespace Pehapkari\RouteUsage\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class RouteVisit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $route;

    /**
     * @ORM\Column(type="text")
     */
    private string $routeParams;

    /**
     * @ORM\Column(type="string")
     */
    private string $controller;

    /**
     * @ORM\Column(type="string")
     */
    private string $uniqueRouteHash;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    public function __construct(string $route, string $routeParams, string $controller, DateTimeInterface $createdAt)
    {
        $this->route = $route;
        $this->routeParams = $routeParams;
        $this->controller = $controller;
        $this->createdAt = $createdAt;
        $this->uniqueRouteHash = sha1($route . $routeParams);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getRouteParams(): string
    {
        return $this->routeParams;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUniqueRouteHash(): string
    {
        return $this->uniqueRouteHash;
    }
}
