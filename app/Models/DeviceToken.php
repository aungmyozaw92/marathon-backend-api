<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    //
	protected $fillable = ['referable_id','referable_type','device_token', 'is_active'];
	protected $casts = [
		'is_active' => 'boolean'
	];
    public function referable()
    {
        return $this->morphTo()->withTrashed();
    }
}
