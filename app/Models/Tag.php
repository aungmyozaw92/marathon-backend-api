<?php

namespace App\Models;

use App\Models\Merchant;
use App\Models\ProductTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tags';

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
    protected $fillable = ['name', 'merchant_id', 'created_by_id', 'updated_by_id', 'deleted_by_id',
                            'created_by_type', 'updated_by_type', 'deleted_by_type'];

    public function product_tags()
    {
        return $this->hasMany(ProductTag::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['name']) && $name = $filter['name']) {
            $query->where('name', 'ILIKE', "%{$name}%");
        }

        if (isset($filter['merchant_id']) && $merchant_id = $filter['merchant_id']) {
            $query->where('merchant_id', $merchant_id);
        }
    }
    
}
