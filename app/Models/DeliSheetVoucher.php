<?php

namespace App\Models;

use App\Models\DeliSheet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DeliSheetVoucher extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'deli_sheet_vouchers';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'return' => 'boolean',
        'delivery_status' => 'boolean',
        'cant_deliver' => 'boolean',
        'is_came_from_mobile' => 'boolean'
    ];

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
    protected $fillable = [
        'delisheet_id', 'note', 'priority', 'voucher_id', 'return', 'ATC_receiver', 'created_by', 'updated_by',
        'deleted_by', 'delivery_status', 'cant_deliver', 'is_came_from_mobile'
    ];

    public function deli_sheet()
    {
        return $this->belongsTo(DeliSheet::class, 'delisheet_id')->withTrashed();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }
}
