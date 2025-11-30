<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('attachments', 'public');

        $attachment = Attachment::create([
            'task_id' => $validated['task_id'],
            'user_id' => auth()->id(),
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        $attachment->load('uploader');

        return response()->json([
            'message' => 'Attachment uploaded successfully',
            'attachment' => $attachment,
        ], 201);
    }

    public function destroy(Attachment $attachment)
    {
        if ($attachment->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted successfully',
        ], 200);
    }
}
