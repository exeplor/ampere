<?php

namespace Ampere\Services\Workshop\Page;

use Ampere\Services\Workshop\Layout\Builder;

/**
 * Class Assets
 * @package Ampere\Services\Workshop\Page
 */
class Assets
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
     * @param string $path
     * @return Assets
     */
    public function css(string $path): self
    {
        $this->builder->addCss($path);
        return $this;
    }

    /**
     * @param string $path
     * @return Assets
     */
    public function js(string $path): self
    {
        $this->builder->addJs($path);
        return $this;
    }
}