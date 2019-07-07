<?php

namespace Ampere\Services;

/**
 * Class Config
 * @package Ampere\Services
 */
class Config
{
    /**
     * @param string $name
     * @return bool
     */
    public static function useSpace(string $name): bool
    {
        $spaces = self::getSpaces();

        if (empty($spaces[$name])) {
            throw new \Exception('Ampere config "' . $name . '" not found');
        }

        \Illuminate\Support\Facades\Config::set([
            '__ampere' => $spaces[$name],
            '__ampereSpaceName' => $name
        ]);

        return true;
    }

    /**
     * @return array
     */
    public static function getSpaces(): array
    {
        return config('ampere', []);
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function load(string $name): bool
    {
        $config = config('ampere', []);
        $config[$name] = include(config_path('ampere/' . $name . '.php'));

        \Illuminate\Support\Facades\Config::set([
            'ampere' => $config
        ]);

        return true;
    }

    /**
     * @return null|string
     */
    public static function getCurrentSpaceName(): ?string
    {
        return config('__ampereSpaceName', null);
    }
}