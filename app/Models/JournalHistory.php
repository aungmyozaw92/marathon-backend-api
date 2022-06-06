<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalHistory extends Model
{
    // use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'journal_histories';

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

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'journal_id', 
        'resourceable_type', 
        'resourceable_id', 
        'log_type', 
        'from_path', 
        'updated_by', 
        'updated_by_name', 
    ];
}
