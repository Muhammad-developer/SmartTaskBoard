<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    public function exportBoardJSON(Request $request)
    {
        $boardId = $request->input('board_id');

        $board = Board::with([
            'columns.tasks.tags',
            'columns.tasks.assignee',
            'columns.tasks.creator',
            'columns.tasks.comments.author',
            'columns.tasks.attachments.uploader'
        ])->findOrFail($boardId);

        $data = [
            'board' => [
                'id' => $board->id,
                'name' => $board->name,
                'description' => $board->description,
                'color' => $board->color,
                'created_at' => $board->created_at,
                'updated_at' => $board->updated_at,
            ],
            'columns' => $board->columns->map(function ($column) {
                return [
                    'id' => $column->id,
                    'name' => $column->name,
                    'position' => $column->position,
                    'tasks' => $column->tasks->map(function ($task) {
                        return [
                            'id' => $task->id,
                            'title' => $task->title,
                            'description' => $task->description,
                            'position' => $task->position,
                            'priority' => $task->priority,
                            'due_date' => $task->due_date,
                            'created_at' => $task->created_at,
                            'updated_at' => $task->updated_at,
                            'assignee' => $task->assignee ? [
                                'id' => $task->assignee->id,
                                'name' => $task->assignee->name,
                                'email' => $task->assignee->email,
                            ] : null,
                            'creator' => $task->creator ? [
                                'id' => $task->creator->id,
                                'name' => $task->creator->name,
                                'email' => $task->creator->email,
                            ] : null,
                            'tags' => $task->tags->map(function ($tag) {
                                return [
                                    'id' => $tag->id,
                                    'name' => $tag->name,
                                    'color' => $tag->color,
                                ];
                            }),
                            'comments' => $task->comments->map(function ($comment) {
                                return [
                                    'id' => $comment->id,
                                    'content' => $comment->content,
                                    'created_at' => $comment->created_at,
                                    'author' => [
                                        'id' => $comment->author->id,
                                        'name' => $comment->author->name,
                                        'email' => $comment->author->email,
                                    ],
                                ];
                            }),
                            'attachments' => $task->attachments->map(function ($attachment) {
                                return [
                                    'id' => $attachment->id,
                                    'file_name' => $attachment->file_name,
                                    'file_size' => $attachment->file_size,
                                    'mime_type' => $attachment->mime_type,
                                    'created_at' => $attachment->created_at,
                                    'uploader' => [
                                        'id' => $attachment->uploader->id,
                                        'name' => $attachment->uploader->name,
                                        'email' => $attachment->uploader->email,
                                    ],
                                ];
                            }),
                        ];
                    }),
                ];
            }),
        ];

        $fileName = 'board_' . $board->id . '_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function exportBoardCSV(Request $request)
    {
        $boardId = $request->input('board_id');

        $board = Board::with([
            'columns.tasks.tags',
            'columns.tasks.assignee',
            'columns.tasks.creator'
        ])->findOrFail($boardId);

        $csvData = [];
        $csvData[] = ['Board', $board->name];
        $csvData[] = ['Description', $board->description];
        $csvData[] = [];
        $csvData[] = ['Column', 'Task ID', 'Title', 'Description', 'Priority', 'Due Date', 'Assigned To', 'Created By', 'Tags', 'Created At', 'Updated At'];

        foreach ($board->columns as $column) {
            foreach ($column->tasks as $task) {
                $csvData[] = [
                    $column->name,
                    $task->id,
                    $task->title,
                    $task->description,
                    $task->priority,
                    $task->due_date ? $task->due_date->format('Y-m-d H:i:s') : '',
                    $task->assignee ? $task->assignee->name : '',
                    $task->creator ? $task->creator->name : '',
                    $task->tags->pluck('name')->implode(', '),
                    $task->created_at->format('Y-m-d H:i:s'),
                    $task->updated_at->format('Y-m-d H:i:s'),
                ];
            }
        }

        $fileName = 'board_' . $board->id . '_' . date('Y-m-d_H-i-s') . '.csv';

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function exportBoardPDF(Request $request)
    {
        $boardId = $request->input('board_id');

        $board = Board::with([
            'columns.tasks.tags',
            'columns.tasks.assignee',
            'columns.tasks.creator'
        ])->findOrFail($boardId);

        $pdf = Pdf::loadView('exports.board-pdf', ['board' => $board]);

        $fileName = 'board_' . $board->id . '_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($fileName);
    }
}
