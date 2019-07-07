<?php

namespace Ampere;

use Ampere\Commands\Builder\MakeCrudCommand;
use Ampere\Commands\Builder\MakePageCommand;
use Ampere\Commands\InstallCommand;
use Ampere\Commands\MigrateCommand;
use Ampere\Services\Config;
use Illuminate\Support\ServiceProvider;

/**
 * Class AmpereServiceProvider
 * @package Ampere
 */
class AmpereServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    private $commands = [
        InstallCommand::class,
        MigrateCommand::class,
        MakePageCommand::class,
        MakeCrudCommand::class
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'ampere.auth' => Middleware\Authenticate::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'ampere' => [
            'ampere.auth'
        ],
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands($this->commands);

        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }

        if ($ampereSpace = $this->getCurrentAmpereSpace()) {

            if (!app()->runningInConsole()) {

                Config::useSpace($ampereSpace);
                $this->loadRoutesFrom(__DIR__ . '/routes.php');
                $this->app['view']->addNamespace('ampere', ampere_path('views'));

            }
        }
    }

    /**
     * @return null|string
     */
    private function getCurrentAmpereSpace(): ?string
    {
        $spaces = Config::getSpaces();

        if (empty($spaces)) {
            return null;
        }

        if (count($spaces) === 1) {
            return array_keys($spaces)[0];

        } else {
            foreach($spaces as $name => $space) {
                if (strpos(request()->path(), $space['app']['url_prefix']) === 0) {
                    return $name;
                }
            }
        }

        return null;
    }
}
