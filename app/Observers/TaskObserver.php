<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        ActivityLog::log(
            'created',
            Task::class,
            $task->id,
            null,
            "Task '{$task->title}' created"
        );
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        $changes = [];
        $fillable_fields = ['title', 'description', 'priority', 'due_date', 'assigned_to', 'column_id'];

        foreach ($fillable_fields as $field) {
            if ($task->wasChanged($field)) {
                $changes[$field] = [
                    'old' => $task->getOriginal($field),
                    'new' => $task->getAttribute($field),
                ];
            }
        }

        if (!empty($changes)) {
            ActivityLog::log(
                'updated',
                Task::class,
                $task->id,
                $changes,
                "Task '{$task->title}' updated"
            );
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        ActivityLog::log(
            'deleted',
            Task::class,
            $task->id,
            null,
            "Task '{$task->title}' deleted"
        );
    }
}
