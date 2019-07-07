<?php

namespace Ampere\Models;

/**
 * Class RolePermission
 *
 * @property int $role_id
 * @property int $permission_id
 *
 * @property-read Permission $permission
 * @property-read Role $role
 *
 * @package Exeplor\Admin\Models
 */
class RolePermission extends Model
{
    /**
     * @var string
     */
    protected $table = 'roles_permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'permission_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
