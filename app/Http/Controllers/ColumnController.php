<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'board_id' => 'required|exists:boards,id',
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'position' => 'nullable|integer',
        ]);

        if (!isset($validated['position'])) {
            $validated['position'] = Column::where('board_id', $validated['board_id'])->max('position') + 1;
        }

        $column = Column::create($validated);

        return response()->json($column, 201);
    }

    public function update(Request $request, Column $column)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'color' => 'sometimes|string|max:7',
            'position' => 'sometimes|integer',
        ]);

        $column->update($validated);

        return response()->json($column);
    }

    public function destroy(Column $column)
    {
        $column->delete();
        return response()->json(null, 204);
    }
}
