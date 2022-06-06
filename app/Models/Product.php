<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Merchant;
use App\Models\Inventory;
use App\Models\ProductTag;
use App\Models\ProductType;
use App\Models\ProductReview;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

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
        'uuid', 'sku', 'merchant_id', 'item_name', 'item_price', 'is_seasonal', 'is_feature', 'product_type_id', 'lwh', 'weight',
        'created_by_id', 'updated_by_id', 'deleted_by_id', 'created_by_type', 'updated_by_type', 'deleted_by_type'
    ];

    // public function getRouteKeyName()
    // {
    //     return 'uuid';
    // }

    public function attachments()
    {
        return $this->morphMany('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    public function attachment()
    {
        return $this->morphOne('App\Models\Attachment', 'resourceable', 'resource_type', 'resource_id');
    }

    /**
     * Relations
     */
    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function product_tags()
    {
        return $this->hasMany(ProductTag::class);
    }

    public function product_variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function product_reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function scopeFilter($query, $filter)
    {
        if ((isset($filter['filter']) && $filter = $filter['filter']) ||
            isset($filter['search']) && $filter = $filter['search']
        ) {
            $query->where('item_name', 'ILIKE', "%{$filter}%")
                ->orWhere('item_price', 'ILIKE', "%{$filter}%")
                ->orWhere('weight', 'ILIKE', "%{$filter}%")
                ->orWhere('lwh', 'ILIKE', "%{$filter}%")
                ->orWhere('sku', 'ILIKE', "%{$filter}%")
                ->orWhereHas('product_tags', function ($q) use ($filter) {
                    $q->whereHas('tag', function ($q) use ($filter) {
                        $q->where('name', 'ILIKE', "%{$filter}%");
                    });
                });
        } else {
            if (
                isset($filter['filter_by_selected']) && $filter = $filter['filter_by_selected']
            ) {
                $query->whereIn('id', explode(",", $filter));
            }
        }
    }
}
