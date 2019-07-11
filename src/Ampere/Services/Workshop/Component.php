<?php

namespace Ampere\Services\Workshop;

use Ampere\Services\Chart\ChartMap;
use Ampere\Services\Grid\Grid;
use Ampere\Services\Workshop\Layout\Builder;

/**
 * Class Component
 * @package Ampere\Services\Workshop
 */
class Component
{
    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var string
     */
    private $view;

    /**
     * @var array
     */
    private $params = [];

    /**
     * Component constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string $view
     * @param array $baseParams
     * @param array $extendedParams
     * @return Component
     */
    public function make(string $view, array $baseParams = [], array $extendedParams = [])
    {
        $component = new self($this->builder);
        $component->view = $view;
        $component->params = array_merge($baseParams, $extendedParams);

        return $component;
    }

    /**
     * @param string $view
     * @param array $baseParams
     * @param array $extendedParams
     */
    public function show(string $view, array $baseParams = [], array $extendedParams = [])
    {
        echo $this->build($view, $baseParams, $extendedParams);
    }

    /**
     * @return void
     */
    public function render()
    {
        $this->show($this->view, $this->params);
    }

    /**
     * @param Grid $grid
     */
    public function grid(Grid $grid)
    {
        $this->show('grid.table', [
            'grid' => $grid
        ]);
    }

    /**
     * @param string|null $title
     * @param ChartMap $chart
     */
    public function chart(string $title = null, ChartMap $chart)
    {
        $this->show('chart.wrap', [
            'chart' => $chart,
            'title' => $title
        ]);
    }

    /**
     * @param string $view
     * @param array $basicParams
     * @param array $extendedParams
     * @return string
     */
    public function build(string $view, array $basicParams = [], array $extendedParams = [])
    {
        $params = array_merge($basicParams, $extendedParams);

        $params['component'] = $this;
        $params['include'] = $this->builder->makeAssetManager();

        $component = View::render('components.' . $view, $params)->render();
        return $component;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build($this->view, $this->params);
    }
}