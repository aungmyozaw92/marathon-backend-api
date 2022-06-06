<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchSheet extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'branch_sheets';

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
        'is_paid' => 'boolean',
        'credit' => 'integer',
        'debit' => 'integer',
        'balance' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branchsheet_invoice', 'branch_id','qty',
        'created_by', 'updated_by', 'deleted_by', 'is_paid'
    ];

    /**
     * Accessors
     */
    // public function getCreditAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDebitAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getBalanceFeeAttribute($value)
    // {
    //     return number_format($value);
    // }

    /**
     * Mutators
     */
    public function setBranchsheetInvoiceAttribute($value)
    {
        $this->attributes['branchsheet_invoice'] = 'B' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relations
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'branch_sheet_vouchers', 'branch_sheet_id', 'voucher_id')
            // ->using(DeliSheetVoucher::class)
            ->as('branch_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        // ->opened();
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('created_at', $date);
        }
        if (isset($filter['branch_id']) && $branch_id = $filter['branch_id']) {
            $query->where('branch_id', $branch_id);
        }
    }
}
