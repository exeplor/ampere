<?php

namespace Ampere\Services;

use Ampere\Facades\Ampere;

/**
 * Class Menu
 * @package Ampere\Services
 */
class Menu
{
    /**
     * @return array
     * @throws \Exception
     */
    public function get(): array
    {
        $basicMenu = $this->generate();
        $customMenu = $this->getCustomMenu();

        if (empty($customMenu)) {
            $customMenu = $this->saveCustomMenu($basicMenu);
        }

        $menu = $this->merge($basicMenu, $customMenu);
        $this->saveCustomMenu($menu);

        return $menu;
    }

    /**
     * @return array
     */
    private function generate(): array
    {
        $router = Ampere::router();

        $currentRoute = $router->getCurrentRouteInfo();
        $routes = $router->getRoutes();

        $menu = [];
        $links = [];

        $hasActiveSection = false;

        foreach($routes as $route) {
            $items = $route['menu'];

            if (empty($items)) {
                continue;
            }

            $rootSectionName = $items[0];

            if (count($items) === 1) {
                if (empty($menu[$rootSectionName])) {
                    $isRootActive = $currentRoute['as'] === $route['as'];
                    $menu[$rootSectionName] = [
                        'title' => $rootSectionName,
                        'icon' => null,
                        'route' => $route['as'],
                        'link' => ampere_route($route['as']),
                        'child' => [],
                        'controller' => $route['class'],
                        'is_active' => $isRootActive,
                        'is_generate' => true
                    ];

                    if ($isRootActive) {
                        $hasActiveSection = true;
                    }
                }
            }

            if (count($items) === 2) {
                if (empty($menu[$rootSectionName])) {
                    $menu[$rootSectionName] = [
                        'title' => $rootSectionName,
                        'icon' => null,
                        'route' => $route['as'],
                        'link' => ampere_route($route['as']),
                        'child' => [],
                        'is_active' => $currentRoute['as'] === $route['as'],
                        'controller' => $route['class'],
                        'is_generate' => true
                    ];
                }

                $isChildActive = $currentRoute['as'] === $route['as'];
                if ($isChildActive) {
                    $menu[$rootSectionName]['is_active'] = true;
                    $hasActiveSection = true;
                }

                $menu[$rootSectionName]['link'] = null;
                $menu[$rootSectionName]['route'] = null;
                $menu[$rootSectionName]['child'][$items[1]] = [
                    'title' => $items[1],
                    'route' => $route['as'],
                    'link' => ampere_route($route['as']),
                    'is_active' => $isChildActive,
                    'is_generate' => true
                ];
            }
        }

        if (!$hasActiveSection) {
            foreach($menu as $name => $root) {
                if ($root['route']) {
                    if (strpos($route['route'], $currentRoute['as']) === 0) {
                        $menu[$name]['is_active'] = true;
                    }
                } else {
                    foreach($root['child'] as $alias => $child) {
                        if (strpos($currentRoute['as'], $child['route']) === 0) {
                            $menu[$name]['child'][$alias]['is_active'] = true;
                            $menu[$name]['is_active'] = true;
                        }
                    }
                }
            }
        }

        return $menu;
    }

    /**
     * @param array $basicMenu
     * @return array
     */
    private function convertBasicToCustom(array $basicMenu): array
    {
        $menu = [];
        foreach($basicMenu as $groupName => $meta) {
            $item = [
                'title' => $meta['title']
            ];

            if (isset($meta['icon'])) {
                $item['icon'] = $meta['icon'];
            }

            if (isset($meta['link'])) {
                $item['link'] = $meta['link'];
            }

            if (isset($meta['child']) && count($meta['child'])) {
                $item['child'] = [];

                foreach($meta['child'] as $childName => $childMeta) {
                    $child = $childMeta['title'];

                    if (empty($childMeta['is_generate'])) {
                        $child = [];

                        $childKeys = ['title', 'route', 'link'];
                        foreach($childKeys as $key) {
                            $child[$key] = empty($childMeta[$key]) ? null : $childMeta[$key];
                        }

                        $child = array_filter($child);
                        $child = count($child) === 1 && isset($child['title']) ? $child['title'] : $child;
                    }

                    $item['child'][$childName] = $child;
                }
            }

            $menu[$groupName] = $item;
        }

        return $menu;
    }

    /**
     * @return array
     */
    private function getCustomMenu(): array
    {
        $path = $this->getCustomPath();

        if (!file_exists($path)) {
            return [];
        }

        return include $path;
    }

    /**
     * @return string
     */
    private function getCustomPath(): string
    {
        return resource_path('ampere/' . Config::getCurrentSpaceName() . '/menu.php');
    }

    /**
     * @param array $menu
     * @return array
     */
    private function saveCustomMenu(array $menu): array
    {
        $fileData = ['<?php ', PHP_EOL . 'return [' . PHP_EOL];

        $preparedMenu = $this->convertBasicToCustom($menu);
        foreach($preparedMenu as $groupName => $meta) {
            $baseMeta = $menu[$groupName];

            $fileData[] = "\t/*";

            if (empty($menu[$groupName]['is_generate'])) {
                $fileData[] = "\t * Custom group";
            } else {
                $fileData[] = "\t * Class " . $baseMeta['controller'];
            }
            $fileData[] = "\t */";
            $fileData[] = "\t" . "'" . $groupName. "' => [";
            $fileData[] = "\t\t" . "'title' => __('" . $meta['title'] . "'),";

            if (isset($meta['link']) && empty($menu[$groupName]['is_generate'])) {
                $fileData[] = "\t\t" . "'link' => '" . $meta['link'] . "',";
            }

            if (isset($meta['icon'])) {
                $fileData[] = "\t\t" . "'icon' => '" . $meta['icon'] . "',";
            }

            if (isset($meta['child']) && count($meta['child']) > 0) {

                $childList = [];
                foreach($meta['child'] as $childName => $childValue) {
                    if (is_array($childValue)) {

                        $innerList = [];
                        foreach($childValue as $key => $value) {
                            if ($key === 'title') {
                                $value = "__('$value')";
                            } else {
                                $value = "'$value'";
                            }

                            $innerList[] = "\t\t\t\t" . "'$key' => $value";
                        }

                        $childList[] = "\t\t\t" . "'$childName' => [" . PHP_EOL . implode("," . "\t\t\t\t". PHP_EOL, $innerList);
                        $childList[] = "\t\t\t]";

                    } else {
                        $childList[] = "\t\t\t" . "'$childName' => __('$childValue')";
                    }
                }

                $fileData[] = "\t\t" . "'child' => [";
                $fileData[] = implode(',' . PHP_EOL, $childList);
                $fileData[] = "\t\t]";
            }

            $fileData[] = "\t]," . PHP_EOL;
        }

        $fileData[] = "];";
        $fileContent = implode(PHP_EOL, $fileData);

        $path = $this->getCustomPath();
        file_put_contents($path, $fileContent);

        return $preparedMenu;
    }

    /**
     * @param array $basicMenu
     * @param array $customMenu
     * @return array
     * @throws \Exception
     */
    private function merge(array $basicMenu, array $customMenu): array
    {
        $menu = [];
        foreach($customMenu as $customName => $customMeta) {
            $basicMeta = $basicMenu[$customName] ?? null;

            if ($basicMeta) {
                $menu[$customName] = $basicMeta;
                $menu[$customName]['title'] = $customMeta['title'];

                if (isset($customMeta['icon'])) {
                    $menu[$customName]['icon'] = $customMeta['icon'];
                }

                $customChild = $customMeta['child'] ?? [];
                $basicChild = $basicMeta['child'] ?? [];
                $childMenu = [];

                foreach($customChild as $childName => $childMeta) {
                    $childMenu[$childName] = $childMeta;

                    if (isset($basicChild[$childName])) {
                        $childMenu[$childName] = $basicChild[$childName];
                        $childMenu[$childName]['title'] = $childMeta;
                    }
                }

                foreach($basicChild as $childName => $childMeta) {
                    if (empty($customChild[$childName])) {
                        $childMenu[$childName] = $childMeta;
                    }
                }

                foreach($childMenu as $childName => $childMeta) {
                    if (gettype($childMeta) === 'string') {
                        unset($childMenu[$childName]);
                        continue;
                    } else {
                        $childMenu[$childName]['is_active'] = $childMenu[$childName]['is_active'] ?? false;

                        if (empty($childMenu[$childName]['title'])) {
                            $childMenu[$childName]['title'] = null;
                        }

                        if (empty($childMenu[$childName]['route'])) {
                            $childMenu[$childName]['route'] = null;
                        }
                    }
                }

                $menu[$customName]['child'] = $childMenu;

            } else {
                $default = ['icon', 'route', 'link', 'route'];
                foreach($default as $key) {
                    $customMeta[$key] = $customMeta[$key] ?? null;
                }

                if (empty($customMeta['child'])) {
                    $customMeta['child'] = [];
                }

                foreach($customMeta['child'] as $childName => $childMeta) {
                    $child = $customMeta['child'][$childName];

                    if (gettype($child) === 'string') {
                        $child = [
                            'title' => $child
                        ];
                    }

                    $child['title'] = $child['title'] ?? $childName;
                    $child['route'] = $child['route'] ?? null;
                    $child['link'] = $child['link'] ?? null;
                    $child['is_active'] = false;

                    $customMeta['child'][$childName] = $child;
                }

                $customMeta['controller'] = 'Custom';
                $customMeta['is_active'] = $customMeta['is_active'] ?? false;

                $menu[$customName] = $customMeta;
            }
        }

        foreach($basicMenu as $basicName => $basicMeta) {
            if (empty($customMenu[$basicName])) {
                $menu[$basicName] = $basicMeta;
            }
        }

        return $menu;
    }

}