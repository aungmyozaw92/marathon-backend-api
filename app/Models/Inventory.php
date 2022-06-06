<?php

namespace App\Models;

use App\Models\Product;
use App\Models\InventoryLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'inventories';

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
        // 'gate_debit' => 'boolean',
        // 'gate_rate' => 'integer'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id','minimum_stock','qty','purchase_price','sale_price','is_refundable','is_taxable','is_fulfilled_by','vendor_name',
        'created_by_id', 'updated_by_id', 'deleted_by_id','created_by_type', 'updated_by_type', 'deleted_by_type'
    ];

    /**
     * scopes
     */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory_logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['product_id']) && $product_id = $filter['product_id']) {
            $query->where('product_id', $product_id);
        }
    }
}
