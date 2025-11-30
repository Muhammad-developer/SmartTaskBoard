<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Tag;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get boards the user has access to
        $boards = Board::where('created_by', $user->id)
            ->orWhereHas('team', function ($query) use ($user) {
                $query->whereHas('members', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->with(['columns.tasks.tags', 'creator', 'team'])
            ->get();

        // Get first board or create default
        $board = $boards->first();

        if (!$board) {
            $board = $this->createDefaultBoard($user);
            $boards = collect([$board]);
        }

        $tags = Tag::all();

        // Get users for assignment (team members or all users)
        $users = $board->team
            ? $board->team->members()->get()
            : \App\Models\User::all();

        return view('board', compact('board', 'boards', 'tags', 'users'));
    }

    public function list()
    {
        return response()->json(Board::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $validated['created_by'] = auth()->id();

        // If team_id is provided, check user has access to team
        if ($validated['team_id'] ?? null) {
            $team = \App\Models\Team::findOrFail($validated['team_id']);
            if (!auth()->user()->canAccessTeam($team)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $board = Board::create($validated);

        return response()->json($board, 201);
    }

    public function show(Board $board)
    {
        $boards = Board::all();
        $board->load(['columns.tasks.tags']);
        $tags = Tag::all();
        return view('board', compact('board', 'boards', 'tags'));
    }

    public function update(Request $request, Board $board)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'color' => 'sometimes|string|max:7',
        ]);

        $board->update($validated);

        return response()->json($board);
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return response()->json(null, 204);
    }

    public function data(Board $board)
    {
        $board->load(['columns.tasks.tags']);
        return response()->json($board);
    }

    private function createDefaultBoard($user)
    {
        $board = Board::create([
            'name' => 'My Task Board',
            'description' => 'Get things done!',
            'color' => '#0ea5e9',
            'created_by' => $user->id,
        ]);

        $columns = [
            ['name' => 'To Do', 'color' => '#ef4444', 'position' => 0],
            ['name' => 'In Progress', 'color' => '#f59e0b', 'position' => 1],
            ['name' => 'Review', 'color' => '#8b5cf6', 'position' => 2],
            ['name' => 'Done', 'color' => '#10b981', 'position' => 3],
        ];

        foreach ($columns as $columnData) {
            $board->columns()->create($columnData);
        }

        $board->load(['columns.tasks.tags']);

        return $board;
    }
}
