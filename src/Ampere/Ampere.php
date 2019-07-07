<?php

namespace Ampere;

use Ampere\Services\Guard;
use Ampere\Services\Router;

/**
 * Class Ampere
 * @package Exeplor\Ampere
 */
class Ampere
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * @var Router
     */
    private $router;

    /**
     * Ampere constructor.
     * @param Guard $guard
     * @param Router $router
     */
    public function __construct(Guard $guard, Router $router)
    {
        $this->guard = $guard;
        $this->router = $router;
    }

    /**
     * @return Guard
     */
    public function guard(): Guard
    {
        return $this->guard;
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->guard->getUser();
    }

    /**
     * @return Router
     */
    public function router(): Router
    {
        return $this->router;
    }
}