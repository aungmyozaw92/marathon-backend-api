<?php

namespace App\Models;

use App\Models\Bank;
use App\Models\Agent;
use App\Models\Merchant;
use App\Models\MerchantAssociate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountInformation extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'account_informations';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean'
    ];
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_no', 'account_name', 'resourceable_type','resourceable_id', 'bank_id',
        'is_default', 'created_by', 'updated_by', 'deleted_by'
    ];


    public function resourceable()
    {
        return $this->morphTo()->withTrashed();
    }

    public function merchant_associate()
    {
        return BelongsToMorph::build($this, MerchantAssociate::class, 'resourceable');
    }

    public function merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'resourceable');
    }

    // public function merchant_associate()
    // {
    //     return BelongsToMorph::build($this, MerchantAssociate::class, 'resourceable');
    // }

    public function agent()
    {
        return BelongsToMorph::build($this, Agent::class, 'resourceable');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class)->withTrashed();
    }
}
