<?php

namespace App\Models;

use App\Models\Voucher;
use App\Models\TrackingStatus;
use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackingVoucher extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tracking_vouchers';

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
    protected $fillable = ['tracking_status_id', 'voucher_id', 'created_by', 'updated_by', 'deleted_by'];

    public function tracking_status()
    {
        return $this->belongsTo(TrackingStatus::class, 'tracking_status_id')->withTrashed();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }
    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }
}
