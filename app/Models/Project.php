<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'status',
        'subdomain',
        'snapshot_path',
        'admin_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function files()
    {
        return $this->hasMany(ProjectFile::class);
    }

    /**
     * Timeline progetto (tabella: project_updates)
     */
    public function updates()
    {
        return $this->hasMany(ProjectUpdate::class)->latest();
    }

    /**
     * Commenti (tabella: project_comments)
     */
    public function comments()
    {
        return $this->hasMany(ProjectComment::class)->latest();
    }
}