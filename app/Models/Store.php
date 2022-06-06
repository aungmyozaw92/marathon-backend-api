<?php

namespace App\Models;

use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        \Config::set('auth.defaults.guard', 'merchant');
        static::addGlobalScope('merchant', function (Builder $builder) {
            $builder->where('merchant_id', auth()->user()->id);
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stores';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'item_price' => 'integer',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'item_name', 'item_price', 'merchant_id', 'created_by', 'updated_by', 'deleted_by',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Accessors
     */
    // public function getItemPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    /**
     * scopes
     */
    public function scopeFilter($query, $filter)
    {
        if (isset($filter['search']) && $search = $filter['search']) {
            $query->where('item_name', 'ILIKE', "%{$search}%")
                ->orWhere('item_price', 'ILIKE', "%{$search}%");
        }
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}
