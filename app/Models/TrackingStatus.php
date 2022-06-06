<?php

namespace App\Models;

use App\Models\Voucher;
use App\Models\TrackingVoucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackingStatus extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tracking_statuses';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'status_en', 'status_mm', 'description', 'description_mm', 'created_by', 'updated_by', 'deleted_by'];

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'tracking_vouchers', 'tracking_status_id', 'voucher_id')
            ->as('tracking_vouchers')
            ->withTimestamps()
            ->withPivot([
                'created_by',
                'updated_by',
                'deleted_by'
            ])->orderBy('id', 'decs');
        // ->opened();
    }

    public function tracking_vouchers()
    {
        return $this->hasMany(TrackingVoucher::class, 'tracking_status_id')->withTrashed();
    }
    public function scopeStatus($query, $value)
    {
        return $query->where('status', $value);
    }
}
