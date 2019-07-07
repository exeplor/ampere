<?php

namespace Ampere\Models;

/**
 * Class Permission
 *
 * @property int $id
 * @property string $title
 * @property string $action
 * @property string $description
 *
 * @package Exeplor\Admin\Models
 */
class Permission extends Model
{
    /**
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'action', 'description'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
