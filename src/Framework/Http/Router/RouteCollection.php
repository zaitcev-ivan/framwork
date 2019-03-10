<?php

namespace Framework\Http\Router;



use Framework\Http\Router\Route\RegexRoute;
use Framework\Http\Router\Route\Route;

class RouteCollection
{
    /**
     * @var Route[]
     */
    private $routes = [];

    public function addRoute(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function any($name, $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new RegexRoute($name, $pattern, $handler, [], $tokens));
    }

    public function get($name, $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new RegexRoute($name, $pattern, $handler, ['GET'], $tokens));
    }

    public function post($name, $pattern, $handler, array $tokens = []): void
    {
        $this->addRoute(new RegexRoute($name, $pattern, $handler, ['POST'], $tokens));
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}