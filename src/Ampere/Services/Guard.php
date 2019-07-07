<?php

namespace Ampere\Services;

use Ampere\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class Guard
 * @package Ampere\Services
 */
class Guard
{
    /**
     * @var
     */
    private $user;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * Guard constructor.
     * @param PermissionService $permissionService
     */
    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function attempt(array $credentials): bool
    {
        $data = collect($credentials)->only(['email', 'password'])->toArray();
        $user = User::where('email', $credentials['email'])->first();

        if (empty($user)) {
            return false;
        }

        if (Hash::check($data['password'], $user->password)) {
            session([
                $this->getSessionSpace('ampere_user_id') => $user->id
            ]);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function logout(): bool
    {
        session([$this->getSessionSpace('ampere_user_id') => null]);
        return true;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        if (empty($this->user)) {
            $userId = session($this->getSessionSpace('ampere_user_id'));

            if (empty($userId)) {
                return false;
            }

            $user = User::find($userId);

            if (!$user) {
                return false;
            }

            $user->permissions = $this->permissionService->getUserPermissions($user);
            $user->roles = $this->permissionService->getUserRoles($user);

            $this->user = $user;
        }

        return $this->user;
    }

    /**
     * @param string $password
     * @return string
     */
    public function getPasswordHash(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * @param string $route
     * @return bool
     */
    public function hasAccess(string $route): bool
    {
        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        $permissions = $user->permissions;

        if (isset($permissions[$route])) {
            return true;
        }

        if (isset($permissions['#']) || isset($permissions['*'])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getSessionSpace(string $key): string
    {
        return Config::getCurrentSpaceName() . ':' . $key;
    }
}