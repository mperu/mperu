<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateOption extends Model
{
    protected $fillable = [
        'key',
        'label',
        'type',
        'price_delta',
        'constraints',
        'is_active',
    ];

    protected $casts = [
        'price_delta' => 'integer',
        'constraints' => 'array',
        'is_active' => 'boolean',
    ];
}