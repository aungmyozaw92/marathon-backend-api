<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MerchantSheet;
use App\Models\LogStatus;
use App\Models\Staff;

class MerchantSheetHistory extends Model
{
    protected $table = 'merchant_sheet_histories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    protected $fillable = ['merchant_sheet_id', 'log_status_id', 'previous', 'next', 'created_by'];

    /**
     * Relations
     */
    public function merchantsheet()
    {
        return $this->belongsTo(MerchantSheet::class)->withTrashed();
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
