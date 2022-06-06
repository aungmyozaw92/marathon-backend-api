<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTag extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_tags';

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
    protected $fillable = ['product_id', 'tag_id',
                            'created_by_id', 'updated_by_id', 'deleted_by_id',
                            'created_by_type', 'updated_by_type', 'deleted_by_type'
                        ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['tag_id']) && $tag_id = $filter['tag_id']) {
            $query->where('tag_id', $tag_id);
        }
        if (isset($filter['product_id']) && $product_id = $filter['product_id']) {
            $query->where('product_id', $product_id);
        }
    }

}
