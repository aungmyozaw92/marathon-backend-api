<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DeliSheet;
use App\Models\LogStatus;
use App\Models\Staff;

class DeliSheetHistory extends Model
{
    protected $table = 'delisheet_histories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    protected $fillable = ['delisheet_id', 'log_status_id', 'previous', 'next', 'created_by'];

    /**
     * Relations
     */
    public function delisheet()
    {
        return $this->belongsTo(DeliSheet::class)->withTrashed();
    }

    public function log_status()
    {
        return $this->belongsTo(LogStatus::class)->withTrashed();
    }

    public function created_by_staff()
    {
        return $this->belongsTo(Staff::class, 'created_by')->withTrashed();
    }
}
