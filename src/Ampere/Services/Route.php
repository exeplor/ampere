<?php

namespace Ampere\Services;

use Ampere\Facades\Ampere;

/**
 * Class Route
 * @package Ampere\Services
 */
class Route
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var array
     */
    private $params = [];

    /**
     * Route constructor.
     * @param string $className
     * @param string $methodName
     * @param array $params
     */
    public function __construct(string $className, string $methodName, array $params = [])
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->params = $params;
    }

    /**
     * @param array $params
     * @return Route
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return string
     */
    public function route(): string
    {
        $routes = Ampere::router()->getRoutes();
        return $routes[$this->className . '@' . $this->methodName]['as'] ?? null;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return ampere_route($this->route(), $this->params);
    }

    /**
     * @return bool
     */
    public function access(): bool
    {
        return Ampere::guard()->hasAccess($this->route());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->url();
    }
}