<?php

namespace Ampere\Services;

use Ampere\Models\Permission;
use Ampere\Models\Role;
use Ampere\Models\RolePermission;
use Ampere\Models\User;
use Ampere\Models\UserRole;
use Illuminate\Support\Collection;

/**
 * Class PermissionService
 * @package Ampere\Services
 */
class PermissionService
{
    /**
     * @param Role $role
     * @param User $user
     * @return UserRole
     * @throws \Exception
     */
    public function attachRoleToUser(Role $role, User $user): UserRole
    {
        $params = [
            'user_id' => $user->id,
            'role_id' => $role->id
        ];

        $userRole = UserRole::where($params)->first();
        if (empty($userRole)) {
            $userRole = UserRole::create($params);
        }

        return $userRole;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function detachAllRolesFromUser(User $user): bool
    {
        return UserRole::where('user_id', $user->id)->delete();
    }

    /**
     * @param Permission $permission
     * @param Role $role
     * @return bool
     */
    public function attachPermissionRole(Permission $permission, Role $role): RolePermission
    {
        $rolePermission = RolePermission::firstOrCreate([
            'permission_id' => $permission->id,
            'role_id' => $role->id
        ]);

        return $rolePermission;
    }

    /**
     * @param Permission $permission
     * @param Role $role
     * @return bool
     */
    public function detachPermissionRole(Permission $permission, Role $role): bool
    {
        return RolePermission::where([
            'permission_id' => $permission->id,
            'role_id' => $role->id
        ])->delete();
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function hasUserRole(User $user): bool
    {
        $roles = $this->getUserRoles($user);
        return count($roles) > 0;
    }

    /**
     * @param User $user
     * @return Collection
     * @throws \Exception
     */
    public function getUserRoles(User $user)
    {
        $roles = UserRole::with(['role'])->where('user_id', $user->id)->get()
            ->pluck('role')->keyBy('alias');

        return $roles;
    }

    /**
     * @param $roles
     * @return Collection
     */
    public function getRolesPermissions($roles)
    {
        $roleIds = $roles->pluck('id')->toArray();
        $rolePermissions = RolePermission::with('permission')->whereIn('role_id', $roleIds)->get();

        return $rolePermissions->toBase()->map(function(RolePermission $rolePermission){
            return $rolePermission->permission;
        });
    }

    /**
     * @param User $user
     * @return Collection
     * @throws \Exception
     */
    public function getUserPermissions(User $user)
    {
        $roles = $this->getUserRoles($user);
        $permissions = $this->getRolesPermissions($roles)->keyBy('action');

        return $permissions;
    }

    /**
     * @param int $id
     * @return Role|null
     */
    public function findRole(int $id): ?Role
    {
        return Role::find($id);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function findRoles(array $ids): array
    {
        return Role::whereIn('id', $ids)->get()->all();
    }

    /**
     * @return Collection
     */
    public function getRoles(): Collection
    {
        return Role::get();
    }

    /**
     * @param string $action
     * @return Permission
     */
    public function findOrCreatePermission(string $action): Permission
    {
        $permission = Permission::where('action', $action)->firstOrCreate([
            'title' => $action,
            'action' => $action
        ]);

        return $permission;
    }
}