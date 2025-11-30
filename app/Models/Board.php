<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Board extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'position',
        'team_id',
        'created_by',
        'archived',
        'archived_at',
    ];

    protected $casts = [
        'position' => 'integer',
        'archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class)->orderBy('position');
    }

    /**
     * Get the team that owns the board.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user who created the board.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if user can view the board.
     */
    public function canBeViewedBy(User $user): bool
    {
        if (!$this->team_id) {
            return true; // Personal boards are viewable by anyone for now
        }
        return $user->canAccessTeam($this->team);
    }

    /**
     * Check if user can edit the board.
     */
    public function canBeEditedBy(User $user): bool
    {
        if (!$this->team_id) {
            return $this->created_by === $user->id;
        }
        return $user->isTeamAdmin($this->team);
    }
}
