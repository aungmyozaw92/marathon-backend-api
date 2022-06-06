<?php

namespace App\Models;

use App\Models\FinanceAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAssetType extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_asset_types';

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
    protected $fillable = [ 'name','accumulated_depreciation_account_id','depreciation_expense_account_id',
                            'depreciation_rate','branch_id','created_by','updated_by','deleted_by'
                        ];
    
    public function accumulated_depreciation_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'accumulated_depreciation_account_id')->withTrashed();
    }

    public function depreciation_expense_account()
    {
        return $this->belongsTo(FinanceAccount::class, 'depreciation_expense_account_id')->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
