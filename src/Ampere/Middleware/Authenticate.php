<?php

namespace Ampere\Middleware;

use Closure;
use Ampere\Facades\Ampere;
use Ampere\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @param null $guard
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws \Exception
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $route = Ampere::router()->getCurrentRouteInfo();

        if ($route && $route['guest']) {
            return $next($request);
        }

        if (Ampere::guard()->hasAccess($route['as'])) {
            return $next($request);
        }

        return redirect(ampere_route('auth.login'));
    }
}
