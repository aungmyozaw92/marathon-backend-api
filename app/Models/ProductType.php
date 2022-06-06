<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'product_types';

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
        'name', 'merchant_id', 'created_by_id', 'updated_by_id', 'deleted_by_id', 'created_by_type', 'updated_by_type', 'deleted_by_type'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
    protected $append = [
        'parent_doc' => '0',
        'child_doc' => '0',
    ];

    public function getParentDocAttribute()
    {
        return $this->append['parent_doc'];
    }
    public function setParentDocAttribute($value)
    {
        $this->append['parent_doc'] = $value;
    }
    public function getChildDocAttribute()
    {
        return $this->append['child_doc'];
    }
    public function setChildDocAttribute($value)
    {
        $this->append['child_doc'] = $value;
    }
}
