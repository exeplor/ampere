<?php

namespace Ampere\Services\Workshop\Layout;

/**
 * Class Layout
 * @package Ampere\Services\Workshop\Layout
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
     * @param string $path
     * @return void
     */
    public function css(string $path)
    {
        echo '<link href="' . ampere_public_path($path) . '" rel="stylesheet">';
    }

    /**
     * @param string $path
     * @return void
     */
    public function js(string $path)
    {
        echo '<script src="' . ampere_public_path($path) . '"></script>';
    }

    /**
     * @return array
     */
    public function getCustomCss(): array
    {
        $assets = $this->builder->getCssAssets();
        return array_map(function($path){
            return ampere_public_path($path);
        }, $assets);
    }

    /**
     * @return array
     */
    public function getCustomJs(): array
    {
        $assets = $this->builder->getJsAssets();
        return array_map(function($path){
            return ampere_public_path($path);
        }, $assets);
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->builder->getTitle();
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->builder->getContent();
    }
}