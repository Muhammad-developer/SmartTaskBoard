<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'column_id',
        'title',
        'description',
        'position',
        'priority',
        'due_date',
        'assigned_to',
        'created_by',
        'estimated_hours',
        'time_spent',
        'cover_image',
        'archived',
        'archived_at',
    ];

    protected $casts = [
        'position' => 'integer',
        'due_date' => 'datetime',
        'archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    protected $appends = ['comments_count', 'attachments_count'];

    public function column(): BelongsTo
    {
        return $this->belongsTo(Column::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag');
    }

    /**
     * Get the user the task is assigned to.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the comments for the task.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the attachments for the task.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Get the checklists for the task.
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class);
    }

    /**
     * Get multiple assignees for the task.
     */
    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignees');
    }

    /**
     * Get comments count
     */
    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    /**
     * Get attachments count
     */
    public function getAttachmentsCountAttribute()
    {
        return $this->attachments()->count();
    }
}
