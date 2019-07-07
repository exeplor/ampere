<?php

namespace Ampere\Controllers\Auth;

use Ampere\Controllers\IndexController;
use Ampere\Controllers\Controller;
use Ampere\Facades\Ampere;

/**
 * Class LoginController
 * @guest
 */
class LoginController extends Controller
{
    /**
     * Show form
     */
    public function index()
    {
        if (Ampere::guard()->getUser()) {
            return $this->redirect(IndexController::route('home'));
        }

        return $this->render('login');
    }

    /**
     * Login submit
     * @post index
     * @middleware throttle:10
     */
    public function submit()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        if (!$this->validateCaptcha()) {
            return back()->withErrors(['email' => 'Invalid captcha'])->withInput();
        }

        if (!Ampere::guard()->attempt($this->request->all())) {
            return back()->withErrors(['email' => 'Email or password incorrect'])->withInput();
        }

        return redirect(ampere_route('home'));
    }

    /**
     * @return bool
     */
    private function validateCaptcha(): bool
    {
        if (!ampere_config('auth.google_captcha.enabled')) {
            return true;
        }

        $data = $this->validate([
            'g-recaptcha-response' => 'required|string'
        ]);

        $opts = ['http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    [
                        'secret' => ampere_config('auth.google_captcha.secret_key'),
                        'response' => $data['g-recaptcha-response']
                    ]
                )
            ]
        ];

        $context = stream_context_create($opts);
        $content = file_get_contents('https://www.google.com/recaptcha/api/siteverify', null, $context);

        $data = json_decode($content, true);

        return isset($data['success']) && $data['success'] === true;
    }
}