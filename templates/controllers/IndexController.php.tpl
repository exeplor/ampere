<?php

namespace {namespace};

use Ampere\Controllers\Controller;
use Ampere\Facades\Ampere;

/**
 * Class IndexController
 */
class IndexController extends Controller
{
    /**
     * Index page
     */
    public function index()
    {
        return redirect(self::route('home'));
    }

    /**
     * Home page
     *
     * @menu Home
     */
    public function home()
    {
        return $this->render('home');
    }

    /**
     * @route logout
     * @menu Logout
     */
    public function logout()
    {
        Ampere::guard()->logout();
        return redirect(self::route('home'));
    }
}