<?php

use Illuminate\Support\Facades\Route;

$group = ['as' => ampere_config('routing.prefix') . '.', 'prefix' => ampere_config('app.url_prefix'), 'middleware' => ['web', 'ampere']];
if ($customGroup = ampere_config('routing.group', [])) {
    $group = array_merge($group, $customGroup);
}

Route::group($group, function(){
    $routes = \Ampere\Facades\Ampere::router()->getRoutes();

    foreach($routes as $route) {
        $method = $route['method'];
        Route::$method($route['route'], $route['controller'])->middleware($route['middleware'])->name($route['as']);
    }
});