<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Branch;
use App\Models\FinancePettyCashItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancePettyCash extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_petty_cashes';

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
        'spend_on',
        'total',
        'fn_paymant_option',
        'staff_id',
        'branch_id',
        'created_by', 'updated_by', 'deleted_by' 
    ];

    public function setInvoiceNoAttribute($value)
    {
        $this->attributes['invoice_no'] = 'PC' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }


    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function finance_petty_cash_items()
    {
        return $this->hasMany(FinancePettyCashItem::class, 'finance_petty_cash_id');
    }
    public function actor_by()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }
}
