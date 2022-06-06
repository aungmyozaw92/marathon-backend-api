<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceExpense extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_expenses';

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
    protected $fillable = [
        'spend_at', 'spend_on', 'total', 
        'expense_invoice', 'branch_id', 'staff_id',  
        'is_approved',
        'fn_paymant_option',
        'created_by', 'updated_by', 'deleted_by'
    ];
    public function setExpenseInvoiceAttribute($value)
    {
        $this->attributes['expense_invoice'] = 'PV' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['is_approved']) && $is_approved = $filter['is_approved']) {
            $query->where('is_approved', $is_approved);
        }
        if (isset($filter['is_paid']) && $is_paid = $filter['is_paid']) {
            $query->where('is_paid', $is_paid);
        }
        if (isset($filter['invoice_no']) && $invoice_no = $filter['invoice_no']) {
            $query->where('expense_invoice', 'ILIKE', "%{$invoice_no}%");
        }
        if (isset($filter['fn_paymant_option']) && $fn_paymant_option = $filter['fn_paymant_option']) {
            $query->where('fn_paymant_option', 'ILIKE', "%{$fn_paymant_option}%");
        }
        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            } else {
                $query->whereDate('created_at', $start_date);
            }
        }
        if (isset($filter['issuer']) && $created_by = $filter['issuer']) {
            $query->where('created_by', '=', $created_by);
        }
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function finance_expense_items()
    {
        return $this->hasMany(FinanceExpenseItem::class);
    }
    public function issuer()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }
    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }
}
