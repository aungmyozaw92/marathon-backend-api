<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\FinanceTax;
use App\Models\FinanceCode;
use App\Models\FinanceGroup;
use App\Models\FinanceNature;
use App\Models\FinanceMasterType;
use App\Models\FinanceAccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceAccount extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_accounts';

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
        'name',
        'code',
        'description',
        'finance_nature_id',
        'finance_master_type_id',
        'finance_account_type_id',
        'finance_group_id',
        'branch_id',
        'finance_tax_id',
        'finance_code_id',
        'actor_id',
        'actor_type',
        'created_by',
        'updated_by',
        'deleted_by' 
    ];

    public function finance_nature()
    {
        return $this->belongsTo(FinanceNature::class)->withTrashed();
    }
    public function finance_master_type()
    {
        return $this->belongsTo(FinanceMasterType::class)->withTrashed();
    }
    public function finance_account_type()
    {
        return $this->belongsTo(FinanceAccountType::class)->withTrashed();
    }
    public function finance_group()
    {
        return $this->belongsTo(FinanceGroup::class)->withTrashed();
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class)->withTrashed();
    }
    public function finance_tax()
    {
        return $this->belongsTo(FinanceTax::class)->withTrashed();
    }
    public function finance_code()
    {
        return $this->belongsTo(FinanceCode::class)->withTrashed();
    }

    public function actorable()
    {
        return $this->morphTo(__FUNCTION__, 'actor_type', 'actor_id');
    }

    public function staff()
    {
        return BelongsToMorph::build($this, Staff::class, 'actorable');
    }

}






