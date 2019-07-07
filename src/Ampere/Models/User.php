<?php

namespace Ampere\Models;

/**
 * Class Permission
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 *
 * @package Exeplor\Admin\Models
 */
class User extends Model
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function roles()
    {
        return $this->hasManyThrough(Role::class, UserRole::class, 'user_id', 'id', 'id', 'role_id');
    }
}
