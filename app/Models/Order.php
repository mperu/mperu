<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'quote_id',
        'status',
        'total_amount',
        'deposit_amount',
        'deposit_paid_at',
        'balance_amount',
        'balance_paid_at',
    ];

    protected $casts = [
        'deposit_paid_at' => 'datetime',
        'balance_paid_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }
}