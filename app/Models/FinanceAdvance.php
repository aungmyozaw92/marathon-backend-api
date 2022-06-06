<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Branch;
use App\Models\FinanceAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAdvance extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_advances';

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
        'branch_id','advance_invoice',  'from_finance_account_id', 'to_finance_account_id', 
        'status', 'staff_id', 'amount', 'reason', 
        'is_approved',
        'is_paid',
        'total_expense',
        'total_advance',
        'refund_reimbursements',
        'finance_expense_id',
        'created_by', 'updated_by', 'deleted_by'
    ];

    public function setAdvanceInvoiceAttribute($value)
    {
        $this->attributes['advance_invoice'] = 'AR' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['staff_id']) && $staff_id = $filter['staff_id']) {
            $query->where('staff_id', '=', $staff_id);
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
        if (isset($filter['invoice_no']) && $invoice_no = $filter['invoice_no']) {
            $query->where('advance_invoice', '=', $invoice_no);
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

    public function from_finance_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'from_finance_account_id');
    }

    public function to_finance_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'to_finance_account_id');
    }

    public function finance_expense()
    {
        return $this->belongsTo(FinanceExpense::class, 'finance_expense_id');
    }
    public function issuer()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }
    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    public function postings()
    {
        return $this->morphMany('App\Models\FinancePosting', 'postingable', 'posting_type', 'posting_type_id');
    }
}
