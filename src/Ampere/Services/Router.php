<?php

namespace Ampere\Services;

use Illuminate\Http\Request;

/**
 * Class Router
 * @package Ampere\Services
 */
class Router
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';
    const METHOD_DELETE = 'delete';

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var AnnotationManager
     */
    private $annotationManager;

    /**
     * @var Request
     */
    private $request;

    /**
     * Router constructor.
     * @param AnnotationManager $annotationManager
     * @param Request $request
     */
    public function __construct(AnnotationManager $annotationManager, Request $request)
    {
        $this->request = $request;
        $this->annotationManager = $annotationManager;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        if (empty($this->routes)) {
            $this->routes = $this->build();
        }

        return $this->routes;
    }

    /**
     * @param string $routeName
     * @param bool $hasPrefix
     * @return array|null
     */
    public function getRouteInfo(string $routeName, bool $hasPrefix = false): ?array
    {
        if ($hasPrefix) {
            $routeName = str_replace(ampere_config('routing.prefix') . '.', '', $routeName);
        }

        $routes = collect($this->getRoutes())->keyBy('as');
        return $routes[$routeName] ?? null;
    }

    /**
     * @return array|null
     */
    public function getCurrentRouteInfo(): ?array
    {
        $routeName = $this->request->route()->getName();
        return $this->getRouteInfo($routeName, true);
    }

    /**
     * @return array
     */
    private function build(): array
    {
        $controllersFolder = ampere_config('routing.folder');
        $controllersNamespace = ampere_config('routing.namespace');

        $controllersPath = base_path($controllersFolder);

        if (empty($controllersNamespace)) {
            return [];
        }

        $classes = $this->annotationManager->getClassAnnotations($controllersPath, $controllersNamespace);

        $routes = [];
        foreach($classes as $class) {
            $annotations = $class['annotations'];

            $controller = [
                'route' => $annotations['route'] ?? null,
                'as' => $annotations['as'] ?? null,
                'guest' => $annotations['guest'] ?? false,
                'menu' => $annotations['menu'] ?? false,
                'middleware' => $annotations['middleware'] ?? null
            ];

            foreach($class['methods'] as $methodName => $annotations) {
                $method = [
                    'route' => $annotations['route'] ?? null,
                    'as' => $annotations['as'] ?? null,
                    'method' => self::METHOD_GET,
                    'guest' => $annotations['guest'] ?? false,
                    'menu' => $annotations['menu'] ?? null,
                    'comment' => $annotations['__comment'],
                    'middleware' => $annotations['middleware'] ?? null,
                    'parentRoute' => null
                ];

                $availableMethods = [self::METHOD_POST, self::METHOD_PUT, self::METHOD_DELETE];
                foreach($availableMethods as $targetMethodName) {
                    if (isset($annotations[$targetMethodName])) {
                        $method['method'] = $targetMethodName;
                        $method['parentRoute'] = $annotations[$targetMethodName] === true ? null : $annotations[$targetMethodName];
                    }
                }

                $route = $this->buildRoute($class['class'], $methodName, $controller, $method, $routes);

                if ($route['function'] === '__construct') {
                    continue;
                }

                $routes[$route['controller']] = $route;
            }
        }

        return $routes;
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array $classAnnotations
     * @param array $methodAnnotations
     * @param array $routes
     * @return array
     * @throws \Exception
     */
    private function buildRoute(string $className, string $methodName, array $classAnnotations, array $methodAnnotations, array $routes = []): array
    {
        $controllerRoute = $classAnnotations['route'];
        $controllerAs = $classAnnotations['as'];

        if (empty($controllerRoute)) {
            $pattern = preg_quote(ampere_config('routing.namespace')) . '\\\(.+?)Controller';
            if (!preg_match('#' . $pattern . '#', $className, $match)) {
                throw new \Exception('Bad class name');
            }

            $controllerRoute = str_replace('\\', '/', strtolower($match[1]));
        }

        if (empty($controllerAs)) {
            $controllerAs = str_replace('/', '.', $controllerRoute);
        }

        if ($controllerRoute === 'index') {
            $controllerAs = $controllerRoute = null;
        }

        $methodRoute = $methodAnnotations['route'];
        $methodAs = $methodAnnotations['as'];

        if (empty($methodRoute)) {
            $methodRoute = strtolower($methodName);
            if ($methodName === 'index') {
                $methodRoute = null;
            }
        }

        if (empty($methodAs)) {
            $methodAs = $methodRoute;
        }

        $methodAs = preg_replace('#/\{.+?\}#', '', $methodAs);

        if ($methodAnnotations['parentRoute']) {
            $methodRoutePath = $className . '@' . $methodAnnotations['parentRoute'];
            $parentRoute = $routes[$methodRoutePath] ?? null;

            if (empty($parentRoute)) {
                throw new \Exception('Method "' . $methodName . '" in controller "' . $className . '" has alias to method "' . $methodAnnotations['parentRoute'] . '", but this method not found in "' . $className . '".');
            }

            $controllerRoute = $parentRoute['route'];
            $controllerAs = $parentRoute['as'];
            $methodRoute = $methodAs = null;
        }

        $routeMenu = array_values(
            array_filter(
                array_merge(
                    preg_split('/\s*>\s/', $classAnnotations['menu']),
                    preg_split('/\s*>\s/', $methodAnnotations['menu'])
                )
            )
        );

        $route = [
            'route' => implode('/', array_filter([$controllerRoute, $methodRoute])),
            'as' => implode('.', array_filter([$controllerAs, $methodAs])),
            'class' => $className,
            'method' => $methodAnnotations['method'],
            'controller' => $className . '@' . $methodName,
            'function' => $methodName,
            'guest' => $classAnnotations['guest'] ?? $methodAnnotations['guest'] ?? false,
            'menu' => $routeMenu,
            'comment' => $methodAnnotations['comment'],
            'middleware' => array_filter(array_merge(explode(',', $classAnnotations['middleware']), explode(',', $methodAnnotations['middleware'])))
        ];

        return $route;
    }
}