<?php

namespace App\Models;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTableOfAuthority extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'finance_table_of_authorities';

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
        'petty_amount',
        'expense_amount',
        'advance_amount',
        'staff_id',
        'manager_id',
        'is_need_approve',
        'created_by', 'updated_by', 'deleted_by' 
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    public function manager()
    {
        return $this->belongsTo(Staff::class, 'manager_id');
    }
}
