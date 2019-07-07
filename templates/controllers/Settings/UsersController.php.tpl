<?php

namespace {namespace};

use Ampere\Models\User;
use Ampere\Services\Grid\Grid;
use Ampere\Services\Helper\SelectSearch;

/**
 * Class UsersController
 */
class UsersController extends \Ampere\Controllers\Settings\UsersController
{
    /**
     * User list
	 * @menu Settings > Users
     */
    public function index(Grid $grid)
    {
        return parent::index($grid);
    }

    /**
     * Edit User
     * @route edit/{model}
     */
    public function edit(User $model)
    {
        return parent::edit($model);
    }

    /**
     * @post edit
     */
    public function update(User $model)
    {
        return parent::update($model);
    }

    /**
     * Create new User
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
    public function delete(User $model)
    {
        return parent::delete($model);
    }

    /**
     * @post
     */
    public function search(SelectSearch $search)
    {
        return parent::search($search);
    }
}