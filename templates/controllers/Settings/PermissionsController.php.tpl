<?php

namespace {namespace};

use Ampere\Services\PermissionService;

/**
 * Class PermissionsController
 *
 * @menu Settings
 */
class PermissionsController extends \Ampere\Controllers\Settings\PermissionsController
{
    /**
     * List of permissions
     *
     * @menu Permissions
     *
     * @param PermissionService $permissionService
     */
    public function index(PermissionService $permissionService)
    {
        return parent::index($permissionService);
    }

    /**
     * Change role permission
     *
     * @route change
     * @post
     *
     * @param PermissionService $permissionService
     */
    public function change(PermissionService $permissionService)
    {
        return parent::change($permissionService);
    }
}