<?php

namespace App\Models;

use App\Models\Voucher;
use App\Models\BranchSheet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchSheetVoucher extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'branch_sheet_vouchers';

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
        'branch_sheet_id', 'voucher_id', 'created_by', 'updated_by', 'deleted_by'
    ];

    public function branch_sheet()
    {
        return $this->belongsTo(BranchSheet::class)->withTrashed();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class)->withTrashed();
    }
}
