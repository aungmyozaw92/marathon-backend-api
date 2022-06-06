<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ReturnSheet;
use App\Models\Staff;
use App\Models\LogStatus;

class ReturnSheetHistory extends Model
{
    protected $table = 'return_sheet_histories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    protected $fillable = ['return_sheet_id', 'log_status_id', 'previous', 'next', 'created_by'];

    /**
     * Relations
     */
    public function delisheet()
    {
        return $this->belongsTo(ReturnSheet::class)->withTrashed();
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
