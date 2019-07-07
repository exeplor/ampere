<?php

namespace Ampere\Services\Workshop\Page;

use Ampere\Services\Workshop\Layout\Builder;

/**
 * Class Layout
 * @package Ampere\Services\Workshop\Page
 */
class Layout
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Layout constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string $name
     * @return Layout
     */
    public function set(string $name): self
    {
        $this->builder->setName($name);
        return $this;
    }

    /**
     * @param string $title
     * @return Layout
     */
    public function title(string $title): self
    {
        $this->builder->setTitle($title);
        return $this;
    }
}