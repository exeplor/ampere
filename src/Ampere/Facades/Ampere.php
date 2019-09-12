<?php

namespace Ampere\Facades;

use Ampere\Services\Guard;
use Ampere\Services\Router;
use App\Models\User;
use Illuminate\Support\Facades\Facade;

/**
 * Class Ampere
 *
 * @method static Guard guard()
 * @method static Router router()
 * @method static User user()
 *
 * @package Encore\Admin\Facades
 */
class Ampere extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Ampere\Ampere::class;
    }
}
