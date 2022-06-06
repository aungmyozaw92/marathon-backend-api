<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDiscount extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_discounts';

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
        'parcel_id','merchant_id','discount_type','amount','min_qty','is_inclusive','is_exclusive','is_foc','start_date','end_date',
        'created_by_id', 'updated_by_id', 'deleted_by_id','created_by_type', 'updated_by_type', 'deleted_by_type'
    ];

    // public function getRouteKeyName()
    // {
    //     return 'uuid';
    // }

    /**
     * Relations
     */
    // public function product_type()
    // {
    //     return $this->belongsTo(ProductType::class);
    // }

    // public function merchant()
    // {
    //     return $this->belongsTo(Merchant::class);
    // }

    // public function product_tags()
    // {
    //     return $this->hasMany(ProductTag::class);
    // }

    // public function inventory()
    // {
    //     return $this->hasOne(Inventory::class);
    // }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['filter']) && $filter = $filter['filter']) {
            $query->where('item_name', 'ILIKE', "%{$filter}%")
                    ->orWhereHas('product_tags', function($q) use ($filter){
                            $q->whereHas('tag', function($q) use ($filter){
                                $q->where('name', 'ILIKE', "%{$filter}%");
                            });
                    });
        }
    }
    
}
