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

class Address extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'addresses';

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
        'customer_id', 'name', 'label',
        'created_by_id', 'updated_by_id', 'deleted_by_id',
        'created_by_type', 'updated_by_type', 'deleted_by_type'
    ];

    /**
     * Relations
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
