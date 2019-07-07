<?php

namespace Ampere\Models;

/**
 * Class RolePermission
 *
 * @property int $user_id
 * @property int $role_id
 *
 * @property-read Role $role
 *
 * @package Exeplor\Admin\Models
 */
class UserRole extends Model
{
    /**
     * @var string
     */
    protected $table = 'users_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'role_id'
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
