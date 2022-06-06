<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BusSheetVoucher extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bus_sheet_vouchers';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_return' => 'boolean',
        'is_paid'   => 'boolean',
        'actual_bus_fee' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bus_sheet_id', 'note', 'priority', 'voucher_id', 'delivery_status_id', 'actual_bus_fee', 'payment_status_id',
        'created_by', 'updated_by', 'deleted_by', 'is_return', 'is_paid', 'bus_sheet_voucher_note', 'bus_sheet_voucher_priority'
    ];

    /**
     * Accessors
     */
    // public function getActualBusFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function bus_sheet()
    {
        return $this->belongsTo(BusSheet::class)->withTrashed();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }
}
