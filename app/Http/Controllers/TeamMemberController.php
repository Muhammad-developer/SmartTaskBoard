<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;

class TeamMemberController extends Controller
{
    use AuthorizesRequests;

    public function invite(Request $request, Team $team)
    {
        $this->authorize('manage-members', $team);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::in(['admin', 'member', 'viewer'])],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($team->members()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['email' => 'This user is already a member of the team.']);
        }

        $team->members()->attach($user->id, ['role' => $validated['role']]);

        return back()->with('success', 'Team member invited successfully!');
    }

    public function updateRole(Request $request, Team $team, User $user)
    {
        $this->authorize('manage-members', $team);

        if ($team->owner_id === $user->id) {
            return back()->withErrors(['error' => 'Cannot change the role of the team owner.']);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'member', 'viewer'])],
        ]);

        $team->members()->updateExistingPivot($user->id, ['role' => $validated['role']]);

        return back()->with('success', 'Member role updated successfully!');
    }

    public function remove(Team $team, User $user)
    {
        $this->authorize('manage-members', $team);

        if ($team->owner_id === $user->id) {
            return back()->withErrors(['error' => 'Cannot remove the team owner.']);
        }

        $team->members()->detach($user->id);

        return back()->with('success', 'Member removed successfully!');
    }

    public function leave(Team $team)
    {
        if ($team->owner_id === auth()->id()) {
            return back()->withErrors(['error' => 'Team owner cannot leave the team. Transfer ownership or delete the team.']);
        }

        $team->members()->detach(auth()->id());

        return redirect()->route('teams.index')
            ->with('success', 'You have left the team successfully.');
    }
}
