<?php

namespace Ampere\Services\Grid;

use Ampere\Services\Route;

/**
 * Class GridAction
 * @package Ampere\Services\Grid
 */
class GridAction
{
    const LEVEL_PRIMARY = 'primary';
    const LEVEL_SUCCESS = 'success';
    const LEVEL_DANGER = 'danger';
    const LEVEL_DEFAULT = 'default';

    /**
     * @var array
     */
    private $params = [
        'route' => null,
        'routeParams' => null,
        'title' => null,
        'level' => self::LEVEL_DEFAULT,
        'attribute' => null
    ];

    /**
     * @param Route|\Closure|string $route
     * @param null|string $key
     * @return GridAction
     */
    public function route($route, string $key = null): self
    {
        $this->params['route'] = $route;
        $this->params['routeParams'] = $key;
        return $this;
    }

    /**
     * @param string $title
     * @return GridAction
     */
    public function title(string $title): self
    {
        $this->params['title'] = $title;
        return $this;
    }

    /**
     * @return GridAction
     */
    public function primary(): self
    {
        $this->params['level'] = self::LEVEL_PRIMARY;
        return $this;
    }

    /**
     * @return GridAction
     */
    public function success(): self
    {
        $this->params['level'] = self::LEVEL_SUCCESS;
        return $this;
    }

    /**
     * @return GridAction
     */
    public function danger(): self
    {
        $this->params['level'] = self::LEVEL_DANGER;
        return $this;
    }

    /**
     * @param string $attribute
     * @return GridAction
     */
    public function attribute(string $attribute): self
    {
        $this->params['attribute'] = $attribute;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->params;
    }
}