<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'user_id',
        'status',

        // importi "a preventivo" (LORDO)
        'total_amount',
        'deposit_amount',
        'balance_amount',

        // fiscale (RITENUTA)
        'fiscal_mode',
        'withholding_rate',
        'withholding_amount',

        // extra fiscale
        'stamp_duty_amount', // ✅ marca da bollo (rimborso spese)

        // totale da pagare dal cliente (NETTO + bollo)
        'net_amount',

        // meta/config
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

        'withholding_amount' => 'decimal:2',
        'stamp_duty_amount' => 'decimal:2', // ✅
        'net_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}