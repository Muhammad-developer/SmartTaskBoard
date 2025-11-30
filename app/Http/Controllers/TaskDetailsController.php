<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskDetailsController extends Controller
{
    /**
     * Get full task details with all related data
     */
    public function show(Task $task)
    {
        $task->load([
            'comments.author',
            'attachments.uploader',
            'checklists.items',
            'assignees',
            'tags',
            'column',
            'creator',
            'assignee'
        ]);

        return response()->json($task);
    }

    /**
     * Update task with new data
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'due_date' => 'nullable|date',
            'column_id' => 'sometimes|exists:columns,id',
            'estimated_hours' => 'nullable|numeric|min:0',
            'time_spent' => 'nullable|numeric|min:0',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Add comment to task
     */
    public function addComment(Request $request, Task $task)
    {
        $validated = $request->validate([
            'content' => 'required|string|min:1',
        ]);

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return response()->json($comment->load('author'), 201);
    }

    /**
     * Delete comment
     */
    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json(null, 204);
    }

    /**
     * Upload attachment
     */
    public function uploadAttachment(Request $request, Task $task)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments/' . $task->id, 'public');

        $attachment = $task->attachments()->create([
            'user_id' => auth()->id(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return response()->json($attachment, 201);
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment($attachmentId)
    {
        $attachment = \App\Models\Attachment::findOrFail($attachmentId);

        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return response()->json(null, 204);
    }

    /**
     * Create checklist
     */
    public function createChecklist(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $checklist = $task->checklists()->create($validated);

        return response()->json($checklist, 201);
    }

    /**
     * Add item to checklist
     */
    public function addChecklistItem(Request $request, $checklistId)
    {
        $checklist = Checklist::findOrFail($checklistId);

        $validated = $request->validate([
            'text' => 'required|string|min:1',
        ]);

        $item = $checklist->items()->create([
            'text' => $validated['text'],
            'completed' => false,
        ]);

        return response()->json($item, 201);
    }

    /**
     * Update checklist item
     */
    public function updateChecklistItem(Request $request, $itemId)
    {
        $item = ChecklistItem::findOrFail($itemId);

        $validated = $request->validate([
            'text' => 'sometimes|string|min:1',
            'completed' => 'sometimes|boolean',
        ]);

        $item->update($validated);

        return response()->json($item);
    }

    /**
     * Delete checklist item
     */
    public function deleteChecklistItem($itemId)
    {
        $item = ChecklistItem::findOrFail($itemId);
        $item->delete();

        return response()->json(null, 204);
    }

    /**
     * Assign user to task
     */
    public function assignUser(Request $request, Task $task)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Prevent duplicate assignments
        $task->assignees()->syncWithoutDetaching([$validated['user_id']]);

        return response()->json($task->assignees);
    }

    /**
     * Remove user from task
     */
    public function unassignUser(Request $request, Task $task)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $task->assignees()->detach($validated['user_id']);

        return response()->json($task->assignees);
    }
}
