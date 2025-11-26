<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model
{
    use SoftDeletes;

    protected $table = 'credit';

    protected $fillable = [
        'company_id',
        'credit_amount',
        'used_credit_amount', 
        'remaining_credit_amount',
        'expiration_date',
        'source'
    ];

    protected $casts = [
        'expiration_date' => 'date'
    ];

    /**
     * Get the company that owns these credits
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
