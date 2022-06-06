<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Waybill;
use App\Models\LogStatus;
use App\Models\Staff;

class WaybillHistory extends Model
{
    protected $table = 'waybill_histories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    protected $fillable = ['waybill_id', 'log_status_id', 'previous', 'next', 'created_by','created_by_type'];

    /**
     * Relations
     */
    public function waybill()
    {
        return $this->belongsTo(Waybill::class)->withTrashed();
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
}
