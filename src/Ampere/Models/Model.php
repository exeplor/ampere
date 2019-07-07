<?php

namespace Ampere\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 * @package Ampere\Models
 */
class Model extends BaseModel
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = ampere_config('db.prefix') . $this->table;
    }
}