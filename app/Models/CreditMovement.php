<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditMovement extends Model
{
    protected $table = 'credit_movement_log';

    protected $fillable = [
        'credit_id',
        'action',
        'amount',
        'company_id'
    ];

    /**
     * Get the credit record this movement belongs to
     */
    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }
}
