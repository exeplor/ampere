<?php

namespace {namespace};

/**
 * Class LoginController
 * @guest
 */
class LoginController extends \Ampere\Controllers\Auth\LoginController
{
    /**
     * Show form
     */
    public function index()
    {
        return parent::index();
    }

    /**
     * Login submit
     * @post index
     * @middleware throttle:10
     */
    public function submit()
    {
        return parent::submit();
    }
}