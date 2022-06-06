<?php

namespace App\Models;

use App\Models\FinanceAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancePettyCashItem extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_petty_cash_items';

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
        'invoice_no',
        'spend_at',
        'description',
        'remark',
        'amount',
        'tax_amount',
        'from_finance_account_id',
        'to_finance_account_id',
        'finance_petty_cash_id',
        'created_by', 'updated_by', 'deleted_by' 
    ];

    public function setInvoiceNoAttribute($value)
    {
        $this->attributes['invoice_no'] = 'PCI' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function from_finance_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'from_finance_account_id');
    }
    public function to_finance_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'to_finance_account_id');
    }

    public function actor_by()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }
}
