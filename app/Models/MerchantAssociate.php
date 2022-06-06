<?php

namespace App\Models;

use App\Models\City;
use App\Models\Zone;
use App\Models\Merchant;
use App\Models\ContactAssociate;
//use App\Models\MerchantAssociate;
use App\Models\MerchantRateCard;
use App\Models\AccountInformation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantAssociate extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'merchant_associates';

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
        'merchant_id', 'label', 'email', 'phone', 'address', 'zone_id', 'city_id', 'is_default', 'created_by', 'updated_by', 'deleted_by'
    ];

    public function getPhoneAttribute()
    {
        return $this->contact_associates()->getPhone();
    }

    public function getEmailAttribute()
    {
        return $this->contact_associates()->getEmail();
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class)->withTrashed();
    }

    public function merchant_rate_cards()
    {
        return $this->hasMany(MerchantRateCard::class);
    }
    public function contact_associates()
    {
        return $this->hasMany(ContactAssociate::class);
    }

    public function phones()
    {
        return $this->hasMany(ContactAssociate::class)->where('type', 'phone');
    }

    public function emails()
    {
        return $this->hasMany(ContactAssociate::class)->where('type', 'email');
    }

    public function city()
    {
        return $this->belongsTo(City::class)->withTrashed();
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class)->withTrashed();
    }

    public function account_informations()
    {
        return $this->morphMany(AccountInformation::class, 'resourceable', 'resourceable_type', 'resourceable_id');
    }
}
