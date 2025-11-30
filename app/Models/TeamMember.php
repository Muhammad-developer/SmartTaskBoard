<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'invited_at',
        'joined_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'joined_at' => 'datetime',
    ];

    /**
     * Get the team.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the member has accepted the invitation.
     */
    public function hasAccepted(): bool
    {
        return $this->joined_at !== null;
    }

    /**
     * Mark the invitation as accepted.
     */
    public function acceptInvitation()
    {
        $this->update(['joined_at' => now()]);
    }
}
