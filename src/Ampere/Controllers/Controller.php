<?php

namespace Ampere\Controllers;

use Ampere\Services\Route;
use Ampere\Services\Workshop\Workshop;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Validator;

/**
 * Class Controller
 * @package Ampere\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Workshop
     */
    protected $workshop;

    /**
     * Controller constructor.
     * @param Request $request
     * @param Workshop $workshop
     */
    public function __construct(Request $request, Workshop $workshop)
    {
        $this->request = $request;
        $this->workshop = $workshop;
    }

    /**
     * @param string $view
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function render(string $view, array $data = [])
    {
        return $this->workshop->page($view, $data);
    }

    /**
     * @param string|Route $route
     * @param array $params
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect($route, array $params = [])
    {
        if ($route instanceof Route) {
            return redirect($route->url());
        }

        return redirect()->route(ampere_prefix('.' . $route), $params);
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function validate(array $rules): array
    {
        /**
         * @var \Illuminate\Validation\Validator $validator
         */
        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            throw new HttpResponseException(back()->withErrors($validator)->withInput());
        }

        return $validator->getData();
    }

    /**
     * @param string $method
     * @param array $params
     * @return Route
     */
    public static function route(string $method, $params = []): Route
    {
        return new Route(get_called_class(), $method, $params);
    }
}