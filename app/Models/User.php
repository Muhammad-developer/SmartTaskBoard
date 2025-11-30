<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'bio',
        'last_active_at',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_active_at' => 'datetime',
        ];
    }

    /**
     * Get the teams that the user owns.
     */
    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    /**
     * Get the teams that the user is a member of.
     */
    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')->withPivot('role', 'joined_at')->withTimestamps();
    }

    /**
     * Get the team member records for this user.
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Check if user has role in a specific team.
     */
    public function hasRoleInTeam(Team $team, $role): bool
    {
        return $this->teamMembers()
            ->where('team_id', $team->id)
            ->where('role', $role)
            ->exists();
    }

    /**
     * Check if user is admin in a specific team.
     */
    public function isTeamAdmin(Team $team): bool
    {
        return $this->isTeamOwner($team) || $this->hasRoleInTeam($team, 'admin');
    }

    /**
     * Check if user is team owner.
     */
    public function isTeamOwner(Team $team): bool
    {
        return $this->id === $team->owner_id;
    }

    /**
     * Check if user can access a team.
     */
    public function canAccessTeam(Team $team): bool
    {
        return $this->isTeamOwner($team) || $this->teams->contains($team);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    /**
     * Get the comments authored by the user.
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    /**
     * Get the attachments uploaded by the user.
     */
    public function attachments()
    {
        return $this->hasMany(\App\Models\Attachment::class);
    }
}
