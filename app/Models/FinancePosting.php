<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Branch;
use App\Models\FinanceAdvance;
use App\Models\FinanceExpenseItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancePosting extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_postings';

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
        'branch_id',
        'posting_invoice',
        'from_finance_account_id',
        'to_finance_account_id',
        'amount',
        'description',
        'status',
        'posting_type',
        'posting_type_id',
        'posting_id',
        'from_actor_type_id',
        'from_actor_id',
        'to_actor_type_id',
        'to_actor_id',
        'created_by', 'updated_by', 'deleted_by'
   ];

    public function setPostingInvoiceAttribute($value)
    {
        $this->attributes['posting_invoice'] = 'PO' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }
    /**
      * scopes
      */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['posting_invoice']) && $posting_invoice = $filter['posting_invoice']) {
            $query->where('posting_invoice', 'ILIKE', "%{$posting_invoice}%");
        }
        if (isset($filter['amount']) && $amount = $filter['amount']) {
            $query->where('amount', 'ILIKE', "%{$amount}%");
        }

        if (isset($filter['status']) && $status = $filter['status']) {
            $query->where('status', 'ILIKE', "%{$status}%");
        }

        if (isset($filter['finance_posting_type']) && $finance_posting_type = $filter['finance_posting_type']) {
            $query->where('finance_posting_type', $finance_posting_type);
        }

        if (isset($filter['finance_posting']) && $finance_posting = $filter['finance_posting']) {
            $query->whereHas('finance_expense_item', function ($qr) use ($finance_posting) {
                $qr->whereHas('finance_expense', function ($q) use ($finance_posting) {
                    $q->where('expense_invoice', 'ILIKE', "%{$finance_posting}%");
                });
            })->orWhereHas('finance_advance', function ($qr) use ($finance_posting) {
                $qr->where('advance_invoice', 'ILIKE', "%{$finance_posting}%");
            });
        }

        if (isset($filter['start_date']) && $start_date = $filter['start_date']) {
            if (isset($filter['end_date']) && $end_date = $filter['end_date']) {
                // $query->whereBetween('date', [$start_date, $end_date]);
                ($start_date == $end_date)
                    ? $query->whereDate('created_at', $start_date)
                    : $query->whereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(0)]);
            // ->orWhereBetween('created_at', [$start_date, \Carbon\Carbon::parse($end_date)->addDays(1)]);
            } else {
                $query->whereDate('created_at', $start_date);
                // ->orWhereDate('created_at', $start_date);
            }
        }

        if (isset($filter['from_account']) && $from_account = $filter['from_account']) {
            $query->whereHas('from_finance_account', function ($qr) use ($from_account) {
                $qr->where('id', $from_account)
                   ->orWhere('code', 'ILIKE', "%{$from_account}%")
                   ->orWhere('name', 'ILIKE', "%{$from_account}%");
                
            });
        }

        if (isset($filter['to_account']) && $to_account = $filter['to_account']) {
            $query->whereHas('to_finance_account', function ($qr) use ($to_account) {
                $qr->where('id', $to_account)
                   ->orWhere('code', 'ILIKE', "%{$to_account}%")
                   ->orWhere('name', 'ILIKE', "%{$to_account}%");
                
            });
        }

        // if (isset($filter['account']) && $account = $filter['account']) {
        //     $query->whereHas('finance_expense_item', function ($qr) use ($account) {
        //         $qr->whereHas('finance_expense', function ($q) use ($account){
        //             $q->where('expense_invoice', 'ILIKE', "%{$account}%");
        //         });
        //     })->orWhereHas('finance_advance', function ($qr) use ($account) {
        //         $qr->where('advance_invoice', 'ILIKE', "%{$account}%");
        //     });
        // }
    }

    public function postingable()
    {
        return $this->morphTo(__FUNCTION__, 'posting_type', 'posting_type_id');
    }
   

    public function finance_expense_item()
    {
        return BelongsToMorph::build($this, FinanceExpenseItem::class, 'postingable');
    }

    public function finance_advance()
    {
        return BelongsToMorph::build($this, FinanceAdvance::class, 'postingable');
    }

    public function from_actorable()
    {
        return $this->morphTo(__FUNCTION__, 'from_actor_type', 'from_actor_type_id');
    }

    public function from_actor_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'from_actorable');
    }

    public function to_actorable()
    {
        return $this->morphTo(__FUNCTION__, 'to_actor_type', 'to_actor_type_id');
    }

    public function to_actor_staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'to_actorable');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function from_finance_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'from_finance_account_id');
    }

    public function to_finance_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'to_finance_account_id');
    }
    public function posting()
    {
        return $this->belongsTo(FinancePosting::class, 'posting_id');
    }
}
