<?php

namespace App\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAsset extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_assets';

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
    protected $fillable = [ 'name','branch_id','asset_type_id','description','serial_no','asset_no',
                            'purchase_price','purchase_date','depreciation_start_date','warranty_month',
                            'depreciation_month','depreciation_rate','accumulated_depreciation_account_id',
                            'depreciation_expense_account_id',
                            'created_by','updated_by','deleted_by'
                        ];

    /**
     * Mutators
     */
    public function setAssetNoAttribute($value)
    {
        $this->attributes['asset_no'] = 'AS' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    public function finance_asset_type()
    {
        return $this->belongsTo(FinanceAssetType::class)->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function depreciation_expense_account()
    {
        return $this->belongsTo(FinanceAccount::class,'depreciation_expense_account_id');
    }
    public function accumulated_depreciation_account()
    {
        return $this->belongsTo(FinanceAccount::class,'accumulated_depreciation_account_id');
    }
}
