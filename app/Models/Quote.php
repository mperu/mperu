<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_amount',
        'deposit_amount',
        'balance_amount',
        'config_json',
        'pdf_path',
        'accepted_at',
    ];

    protected $casts = [
        'config_json' => 'array',
        'accepted_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}