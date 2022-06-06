<?php

namespace App\Models;

use App\Models\Merchant;
use App\Models\MerchantAssociate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactAssociate extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contact_associates';

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
        'merchant_id','merchant_associate_id', 'type', 'value', 'created_by', 'updated_by', 'deleted_by'
    ];

    public function scopeGetPhone($query)
    {
        return $query->where('type', 'phone')->get()->pluck('value')->toArray();
    }

    public function scopeGetEmail($query)
    {
        return $query->where('type', 'email')->get()->pluck('value')->toArray();
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class)->withTrashed();
    }

    public function merchant_associate()
    {
        return $this->belongsTo(MerchantAssociate::class);
    }
}
