<?php

namespace Ampere\Models;

/**
 * Class Role
 *
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property string $description
 *
 * @property-read Permission[] $permissions
 *
 * @package Exeplor\Admin\Models
 */
class Role extends Model
{
    /**
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'alias', 'description'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function permissions()
    {
        return $this->hasManyThrough(Permission::class, RolePermission::class, 'role_id', 'id', 'id', 'permission_id');
    }
}
