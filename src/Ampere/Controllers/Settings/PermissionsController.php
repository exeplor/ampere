<?php

namespace Ampere\Controllers\Settings;

use Ampere\Controllers\Controller;
use Ampere\Facades\Ampere;
use Ampere\Services\PermissionService;

/**
 * Class PermissionsController
 *
 * @menu Settings
 * @package {namespace}\Settings
 */
class PermissionsController extends Controller
{
    /**
     * List of permissions
     *
     * @menu Permissions
     *
     * @param PermissionService $permissionService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(PermissionService $permissionService)
    {
        $routes = Ampere::router()->getRoutes();
        $roles = $permissionService->getRoles();

        $list = [];
        foreach($routes as $route) {
            if ($route['guest']) {
                continue;
            }

            $list[$route['as']] = [
                'title' => $route['comment'],
                'menu' => $route['menu'],
                'roles' => []
            ];

            foreach($roles as $role) {
                $actions = $role->permissions->keyBy('action');
                $list[$route['as']]['roles'][$role->id] = $actions->has($route['as']) || $actions->has('*') || $actions->has('#');
            }
        }

        return $this->render('settings.permissions', [
            'list' => $list,
            'roles' => $roles
        ]);
    }

    /**
     * Change role permission
     *
     * @route change
     * @post
     *
     * @param PermissionService $permissionService
     * @return \Illuminate\Http\JsonResponse
     */
    public function change(PermissionService $permissionService)
    {
        $request = $this->validate([
            'action' => 'required',
            'role_id' => 'required'
        ]);

        $routes = Ampere::router()->getRoutes();

        if (!collect($routes)->keyBy('as')->has($request['action'])) {
            return response()->json(['status' => 'error']);
        }

        $permission = $permissionService->findOrCreatePermission($request['action']);
        $role = $permissionService->findRole($request['role_id']);

        if ($request['method'] === 'attach') {
            $permissionService->attachPermissionRole($permission, $role);
        } else {
            $permissionService->detachPermissionRole($permission, $role);
        }

        return response()->json(['status' => 'success']);
    }
}