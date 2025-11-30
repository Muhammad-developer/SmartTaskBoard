<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        return $user->canAccessTeam($team);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        return $user->isTeamAdmin($team);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->isTeamOwner($team);
    }

    /**
     * Determine whether the user can manage members.
     */
    public function manageMembers(User $user, Team $team): bool
    {
        return $user->isTeamAdmin($team);
    }
}
