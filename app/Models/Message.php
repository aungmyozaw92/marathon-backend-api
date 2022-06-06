<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messages';

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
    protected $fillable = ['staff_id', 'referable_type', 'referable_id', 'message_text'];

    /**
     * Get the owning referable model.
     */
    public function referable()
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * Relations
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class)->withTrashed();
    }
    public function messenger()
    {
        return $this->morphTo(__FUNCTION__, 'messenger_type', 'messenger_id')->withTrashed();
    }
}
