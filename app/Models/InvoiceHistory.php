<?php

namespace App\Models;

use App\Models\Staff;
use App\Models\Invoice;
use App\Models\LogStatus;
use App\Models\InvoiceJournal;
use Illuminate\Database\Eloquent\Model;

class InvoiceHistory extends Model
{
    protected $table = 'invoice_histories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    protected $fillable = ['invoice_id','invoice_journal_id', 'log_status_id', 'previous', 'next', 'created_by','remark'];

    /**
     * Relations
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class)->withTrashed();
    }

    public function invoice_journal()
    {
        return $this->belongsTo(InvoiceJournal::class)->withTrashed();
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
