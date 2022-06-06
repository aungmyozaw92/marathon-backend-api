<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
    protected $table = 'staff_role';

    protected $guard_name = 'api';

    protected $fillable = [
        'staff_id', 'role_id'
    ];
}
