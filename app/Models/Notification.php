<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'actor_id',
        'type',
        'notifiable_id',
        'notifiable_type',
        'read_at',
    ];

    public $timestamps = false;

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($notification) {
            $notification->created_at = now();
        });
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
