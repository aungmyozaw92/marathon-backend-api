<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\FinanceAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceConfig extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_configs';

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
        'finance_account_id','branch_id', 'screen', 'created_by', 'updated_by', 'deleted_by','to_finance_account_id' 
    ];

    public function finance_account()
    {
        return $this->belongsTo(FinanceAccount::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function to_finance_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'to_finance_account_id');
    }
}
