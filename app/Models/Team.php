<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'slug',
        'avatar',
    ];

    /**
     * Get the owner of the team.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the members of the team.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_members')->withPivot('role', 'joined_at', 'invited_at')->withTimestamps();
    }

    /**
     * Get the team member records.
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    /**
     * Get the boards for this team.
     */
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    /**
     * Add a member to the team.
     */
    public function addMember(User $user, $role = 'member')
    {
        if (!$this->members->contains($user)) {
            $this->members()->attach($user->id, [
                'role' => $role,
                'joined_at' => now(),
            ]);
        }
    }

    /**
     * Remove a member from the team.
     */
    public function removeMember(User $user)
    {
        $this->members()->detach($user->id);
    }

    /**
     * Update member role.
     */
    public function updateMemberRole(User $user, $role)
    {
        $this->members()->updateExistingPivot($user->id, ['role' => $role]);
    }

    /**
     * Check if a user is a member of the team.
     */
    public function hasMember(User $user): bool
    {
        return $this->members->contains($user);
    }

    /**
     * Get all admins of the team.
     */
    public function admins()
    {
        return $this->teamMembers()->where('role', 'admin')->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name) . '-' . Str::random(8);
            }
        });
    }
}
