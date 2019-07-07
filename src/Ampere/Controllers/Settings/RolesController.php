<?php

namespace Ampere\Controllers\Settings;

use Ampere\Controllers\Controller;
use Ampere\Models\Role;
use Ampere\Services\Grid\Grid;

/**
 * Class RolesController
 * @package {namespace}\Settings
 */
class RolesController extends Controller
{
    /**
     * Role list
	 * @menu Settings > Roles
     */
    public function index(Grid $grid)
    {
		$grid
			->column('id', '#')->strict()->sortable()->asc()
			->column('title')->search()
			->column('alias')->search();

        $grid->action('Edit')->primary()->route(self::route('edit'), 'id');
        $grid->action('Delete', 'delete')->danger()->route(self::route('delete'), 'id');

        $grid->model(Role::class)->limit(32)->search();

        return $this->render('settings.roles.index', compact('grid'));
    }

    /**
     * Edit Role
     * @route edit/{model}
     */
    public function edit(Role $model)
    {
        return $this->render('settings.roles.form', compact('model'));
    }

    /**
     * @post edit
     */
    public function update(Role $model)
    {
        $model->update($this->form());
        return redirect(self::route('index'))->with('success', __('Role successfully updated'));
    }

    /**
     * Create new Role
     * @route create
     */
    public function create()
    {
        return $this->render('settings.roles.form');
    }

    /**
     * @post create
     */
    public function store()
    {
        Role::create($this->form());
        return redirect(self::route('index'))->with('success', __('Role successfully created'));
    }

    /**
     * @route delete/{model}
     * @delete
     */
    public function delete(Role $model)
    {
        $model->delete();
        return redirect(self::route('index'));
    }

    /**
     * @return array
     */
    private function form(): array
    {
        $fields = [
			'title' => ['required', 'string'], 
			'description' => ['required', 'string'], 
			'alias' => ['required', 'string']
        ];

        return collect($this->validate($fields))->only(array_keys($fields))->toArray();
    }
}