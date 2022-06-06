<?php

namespace App\Models;

use App\Models\Parcel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParcelItem extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'parcel_items';

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
        'item_price' => 'integer',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parcel_id', 'item_name', 'item_qty', 'item_price', 'product_id', 'item_status','weight','lwh' ,
        'created_by', 'updated_by', 'deleted_by'
    ];

    /**
     * Accessors
     */
    // public function getItemPriceAttribute($value)
    // {
    //     return number_format($value);
    // }

    public function setItemPriceAttribute($value)
    {
        $this->attributes['item_price'] = number_format((float)$value, 2, '.', '');
    }
    // public function setItemQtyAttribute($value)
    // {
    //     $this->attributes['item_qty'] = number_format((float)$value, 2, '.', '');
    // }

    public function parcel()
    {
        return $this->belongsTo(Parcel::class)->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
