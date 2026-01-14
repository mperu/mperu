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

    /**
     * Relazione storica/flessibile (se un domani vuoi più ordini per lo stesso preventivo)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relazione principale per MVP: 1 quote -> 1 order
     * (quello creato quando l'utente accetta il preventivo)
     */
    public function order()
    {
        return $this->hasOne(Order::class);
    }
}