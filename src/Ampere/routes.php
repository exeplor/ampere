<?php

use Illuminate\Support\Facades\Route;

Route::group(['as' => ampere_config('routing.prefix') . '.', 'prefix' => ampere_config('app.url_prefix'), 'middleware' => ['web', 'ampere']], function(){
    $routes = \Ampere\Facades\Ampere::router()->getRoutes();

    foreach($routes as $route) {
        $method = $route['method'];
        Route::$method($route['route'], $route['controller'])->middleware($route['middleware'])->name($route['as']);
    }
});