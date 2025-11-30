<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $teams = auth()->user()->teams()->with('owner', 'members')->get();

        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => auth()->id(),
        ]);

        $team->members()->attach(auth()->id(), ['role' => 'admin']);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    public function show(Team $team)
    {
        $this->authorize('view', $team);

        $team->load(['owner', 'members' => function ($query) {
            $query->withPivot('role');
        }]);

        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $this->authorize('update', $team);

        return view('teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team->update($validated);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team)
    {
        $this->authorize('delete', $team);

        $team->members()->detach();
        $team->delete();

        return redirect()->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }
}
