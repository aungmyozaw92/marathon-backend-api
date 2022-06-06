<?php

namespace App\Models;

use App\Models\Pickup;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentSheet extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agent_sheets';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_paid' => 'boolean',
        'total_commission_amount' => 'integer',
        'credit' => 'integer',
        'debit' => 'integer',
        'balance' => 'integer',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agentsheet_invoice', 'agent_id', 'qty','total_commission_amount',
        'created_by', 'updated_by', 'deleted_by', 'is_paid'
    ];

    /**
     * Accessors
     */
    // public function getTotalCommissionAmountAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getCreditAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getDebitAttribute($value)
    // {
    //     return number_format($value);
    // }

    // public function getBalanceAttribute($value)
    // {
    //     return number_format($value);
    // }

    /**
     * Mutators
     */
    public function setAgentsheetInvoiceAttribute($value)
    {
        $this->attributes['agent_sheet_invoice'] = 'M' . str_pad($value, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Relations
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class)->withTrashed();
    }

    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'agent_sheet_vouchers', 'agent_sheet_id', 'voucher_id')
            ->as('agent_sheet_vouchers')
            ->withTimestamps()
            ->withPivot([
                'created_by',
                'updated_by',
                'deleted_by'
            ]);
        // ->opened();
    }

    public function scopeFilter($query, $filter)
    {
        if (isset($filter['date']) && $date = $filter['date']) {
            $query->whereDate('created_at', $date);
        }
        if (isset($filter['agent_id']) && $agent_id = $filter['agent_id']) {
            $query->where('agent_id', $agent_id);
        }
    }
}
