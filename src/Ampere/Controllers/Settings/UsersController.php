<?php

namespace Ampere\Controllers\Settings;

use Ampere\Controllers\Controller;
use Ampere\Facades\Ampere;
use Ampere\Models\Role;
use Ampere\Models\User;
use Ampere\Services\Grid\Grid;
use Ampere\Services\Helper\SelectSearch;
use Ampere\Services\PermissionService;

/**
 * Class UsersController
 * @package {namespace}\Settings
 */
class UsersController extends Controller
{
    /**
     * User list
	 * @menu Settings > Users
     */
    public function index(Grid $grid)
    {
		$grid
			->column('id', '#')->strict()->sortable()->asc()
			->column('name')->search()
			->column('email')->search()
			->column('created_at')->date()->sortable();

        $grid->action('Edit')->primary()->route(self::route('edit'), 'id');
        $grid->action('Delete', 'delete')->danger()->route(self::route('delete'), 'id');

        $grid->model(User::class)->limit(32)->search();

        return $this->render('settings.users.index', compact('grid'));
    }

    /**
     * Edit User
     * @route edit/{model}
     */
    public function edit(User $model)
    {
        return $this->render('settings.users.form', compact('model'));
    }

    /**
     * @post edit
     */
    public function update(User $model)
    {
        $model->update($this->form());
        $this->updateRoles($model);
        return redirect(self::route('index'))->with('success', __('User successfully updated'));
    }

    /**
     * Create new User
     * @route create
     */
    public function create()
    {
        return $this->render('settings.users.form');
    }

    /**
     * @post create
     */
    public function store()
    {
        $model = User::create($this->form());
        $this->updateRoles($model);

        return redirect(self::route('index'))->with('success', __('User successfully created'));
    }

    /**
     * @route delete/{model}
     * @delete
     */
    public function delete(User $model)
    {
        $model->delete();
        return redirect(self::route('index'));
    }

    /**
     * @post
     */
    public function search(SelectSearch $search)
    {
        $search->add('roles', Role::class, ['title', 'alias'], 'title');

        return $search->response();
    }

    /**
     * @return array
     */
    private function form(): array
    {
        $fields = [
            'name' => ['string'],
            'email' => ['required', 'string'],
            'password' => ['nullable', 'string']
        ];

        $data = collect($this->validate($fields))->only(array_keys($fields))->toArray();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Ampere::guard()->getPasswordHash($data['password']);
        }

        return $data;
    }

    /**
     * @return array
     */
    private function roles(): array
    {
        $fields = [
            'roles' => 'array'
        ];

        return collect($this->validate($fields))->get('roles');
    }

    /**
     * @param User $user
     */
    private function updateRoles(User $user)
    {
        $permissionService = resolve(PermissionService::class);

        $permissionService->detachAllRolesFromUser($user);

        if ($roles = $this->roles()) {
            $roles = $permissionService->findRoles($roles);

            foreach($roles as $role) {
                $permissionService->attachRoleToUser($role, $user);
            }
        }
    }
}