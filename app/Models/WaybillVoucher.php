<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WaybillVoucher extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'waybill_vouchers';

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
    protected $dates = [ 'deleted_at' ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'waybill_id', 'voucher_id', 'note', 'priority','status', 'is_came_from_partner','created_by', 'updated_by', 'deleted_by'
    ];

    public function waybill()
    {
        return $this->belongsTo(Waybill::class, 'waybill_id')->withTrashed();
    }

}
