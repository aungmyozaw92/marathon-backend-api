<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\FinanceExpense;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceExpenseItem extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_expense_items';

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
       'expense_item_invoice', 'description', 'qty', 'spend_at', 
       'from_finance_account_id', 'to_finance_account_id', 'amount', 
       'finance_expense_id', 'tax_amount', 'remark',
       'created_by', 'updated_by', 'deleted_by'
    ];

    public function setExpenseItemInvoiceAttribute($value)
    {
        $this->attributes['expense_item_invoice'] = 'PVI' . str_pad($value, 6, '0', STR_PAD_LEFT);
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
        return $this->belongsTo(FinanceExpense::class);
    }

    public function postings()
    {
        return $this->morphMany('App\Models\FinancePosting', 'postingable', 'posting_type', 'posting_type_id');
    }
}
