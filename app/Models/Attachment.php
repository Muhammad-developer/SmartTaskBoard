<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'task_id',
        'user_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
    ];

    public $timestamps = false;

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($attachment) {
            $attachment->created_at = now();
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
