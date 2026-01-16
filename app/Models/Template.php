<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'base_price',
        'preview_image',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'integer',
        'is_active' => 'boolean',
    ];
}