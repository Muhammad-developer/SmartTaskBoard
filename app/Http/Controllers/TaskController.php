<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task = Task::create([
            'column_id' => $validated['column_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ?? null,
            'position' => Task::where('column_id', $validated['column_id'])->max('position') + 1,
        ]);

        if (isset($validated['tags'])) {
            $task->tags()->sync($validated['tags']);
        }

        $task->load('tags');

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $task->update($validated);

        if (isset($validated['tags'])) {
            $task->tags()->sync($validated['tags']);
        }

        $task->load('tags');

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    public function move(Request $request, Task $task)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:columns,id',
            'position' => 'required|integer|min:0',
        ]);

        $oldColumnId = $task->column_id;
        $newColumnId = $validated['column_id'];
        $newPosition = $validated['position'];

        if ($oldColumnId !== $newColumnId) {
            Task::where('column_id', $oldColumnId)
                ->where('position', '>', $task->position)
                ->decrement('position');

            Task::where('column_id', $newColumnId)
                ->where('position', '>=', $newPosition)
                ->increment('position');

            $task->column_id = $newColumnId;
            $task->position = $newPosition;
            $task->save();
        } else {
            $oldPosition = $task->position;

            if ($newPosition < $oldPosition) {
                Task::where('column_id', $newColumnId)
                    ->whereBetween('position', [$newPosition, $oldPosition - 1])
                    ->increment('position');
            } else {
                Task::where('column_id', $newColumnId)
                    ->whereBetween('position', [$oldPosition + 1, $newPosition])
                    ->decrement('position');
            }

            $task->position = $newPosition;
            $task->save();
        }

        $task->load('tags');

        return response()->json($task);
    }
}
