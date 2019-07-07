<?php

namespace {namespace};

use Ampere\Services\Grid\Grid;
use Ampere\Models\Role;

/**
 * Class RolesController
 */
class RolesController extends \Ampere\Controllers\Settings\RolesController
{
    /**
     * Role list
	 * @menu Settings > Roles
     */
    public function index(Grid $grid)
    {
        return parent::index($grid);
    }

    /**
     * Edit Role
     * @route edit/{model}
     */
    public function edit(Role $model)
    {
        return parent::edit($model);
    }

    /**
     * @post edit
     */
    public function update(Role $model)
    {
        return parent::update($model);
    }

    /**
     * Create new Role
     * @route create
     */
    public function create()
    {
        return parent::create();
    }

    /**
     * @post create
     */
    public function store()
    {
        return parent::store();
    }

    /**
     * @route delete/{model}
     * @delete
     */
    public function delete(Role $model)
    {
        return parent::delete($model);
    }
}