<?php

namespace App\Models;

use App\Models\Qr;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QrAssociate extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'qr_associates';

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
        'qr_id', 'qr_code', 'valid', 'created_by', 'updated_by', 'deleted_by'
    ];


    /**
     * Relations
     */
    public function qr()
    {
        return $this->belongsTo(Qr::class);
    }

    public function voucher()
    {
        return $this->hasOne(Voucher::class);
    }
}
