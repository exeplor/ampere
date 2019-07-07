<?php

if (!function_exists('ampere_prefix')) {

    /**
     * @param string|null $postfix
     * @return string
     */
    function ampere_prefix(string $postfix = null): string
    {
        return 'ampere' . $postfix;
    }

}

if (!function_exists('ampere_config')) {

    /**
     * @param string|null $name
     * @param null $default
     * @return \Illuminate\Config\Repository|mixed
     */
    function ampere_config(string $name = null, $default = null)
    {
        return config('__ampere' . ($name ? '.' . $name : null), $default);
    }

}

if (!function_exists('ampere_route')) {

    /**
     * @param string $name
     * @param mixed ...$params
     * @return \Illuminate\Config\Repository|mixed
     */
    function ampere_route(string $name, ...$params)
    {
        if (count($params) > 0 && gettype($params[0]) === 'array') {
            $params = $params[0];
        }

        return route(ampere_config('routing.prefix') . '.' . $name, $params);
    }

}

if (!function_exists('ampere_public_path')) {

    /**
     * @param string $path
     * @return string
     */
    function ampere_public_path(string $path)
    {
        $assetsPath = \Illuminate\Support\Str::finish(ampere_config('install.assets_folder', 'vendor'), '/');
        $path = '/' . $assetsPath . $path;

        return str_replace('//', '/', $path);
    }

}

if (!function_exists('ampere_path')) {

    /**
     * @param string $path
     * @return string
     */
    function ampere_path(string $path)
    {
        $assetsPath = realpath(__DIR__ . '/../../');
        return $assetsPath . '/' . $path;
    }

}

if (!function_exists('ampere_controller')) {

    /**
     * @param string $controller
     * @return string
     */
    function ampere_controller(string $controller)
    {
        return ampere_config('routing.namespace') . '\\' . $controller;
    }

}