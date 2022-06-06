<?php

namespace App\Models;

use App\Models\Merchant;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationMeta extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'variation_metas';

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
        'merchant_id', 'value', 'key', 'created_by_id', 'updated_by_id', 'deleted_by_id','created_by_type', 'updated_by_type', 'deleted_by_type'
    ];

    /**
     * Relations
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function product_variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['filter']) && $filter = $filter['filter']) {
            $query->where('key', 'ILIKE', "%{$filter}%")
                ->orWhere('value', 'ILIKE', "%{$filter}%");
        }
    }
}
