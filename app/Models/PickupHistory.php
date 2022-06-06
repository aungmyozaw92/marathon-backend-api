<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Merchant;
use App\Models\Pickup;
use App\Models\LogStatus;
use Illuminate\Database\Eloquent\Model;

class PickupHistory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pickup_histories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['pickup_id', 'log_status_id', 'previous', 'next', 'created_by'];

    /**
     * Relations
     */
    public function pickup()
    {
        return $this->belongsTo(Pickup::class)->withTrashed();
    }

    public function log_status()
    {
        return $this->belongsTo(LogStatus::class)->withTrashed();
    }


    public function createable()
    {
        return $this->morphTo(__FUNCTION__, 'created_by_type', 'created_by')->withTrashed();
    }
    public function created_by_staff()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }
    public function created_by_merchant()
    {
        return BelongsToMorph::build($this, Merchant::class, 'created_by');
    }
}
