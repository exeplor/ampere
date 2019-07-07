<?php

namespace Ampere\Services\Workshop;

/**
 * Class View
 * @package Ampere\Services\Workshop
 */
class View
{
    /**
     * @param string $name
     * @param array $params
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function render(string $name, array $params = [])
    {
        $customViewPath = ampere_config('views.name') . '.' . $name;
        if (view()->exists($customViewPath)) {
            return view($customViewPath, $params);
        }

        $baseViewPath = 'ampere::' . $name;
        if (view()->exists($baseViewPath)) {
            return view($baseViewPath, $params);
        }

        return view($customViewPath);
    }
}