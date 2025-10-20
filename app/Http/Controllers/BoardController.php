<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Tag;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::all();
        $board = Board::with(['columns.tasks.tags'])->first();

        if (!$board) {
            $board = $this->createDefaultBoard();
            $boards = Board::all();
        }

        $tags = Tag::all();

        return view('board', compact('board', 'boards', 'tags'));
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
        ]);

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

    private function createDefaultBoard()
    {
        $board = Board::create([
            'name' => 'My Task Board',
            'description' => 'Get things done!',
            'color' => '#0ea5e9',
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
