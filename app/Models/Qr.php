<?php

namespace App\Models;

use App\Models\Agent;
use App\Models\Customer;
use App\Models\Merchant;
use App\Models\QrAssociate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Qr extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'qrs';

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
        'qty', 'actor_type', 'actor_id', 'created_by', 'updated_by', 'deleted_by'
    ];

    /**
     * Relationship
     */
    public function qr_associates()
    {
        return $this->hasMany(QrAssociate::class);
    }

    public function actor()
    {
        return $this->morphTo()->withTrashed();
    }

    public function merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'actor');
    }

    public function customer()
    {
        return BelongsToMorph::build($this, Customer::class, 'actor');
    }

    public function agent()
    {
        return BelongsToMorph::build($this, Agent::class, 'actor');
    }
}
