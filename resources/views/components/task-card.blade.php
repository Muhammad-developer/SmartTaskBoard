<!-- Enhanced Task Card Component -->
<div
    class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all p-4 cursor-grab active:cursor-grabbing"
    draggable="true"
    data-task-id="{{ $task->id }}"
    data-task-priority="{{ $task->priority }}"
    data-task-assignee="{{ $task->assigned_to }}"
    data-task-tags="{{ $task->tags->pluck('id')->join(',') }}"
    data-task-title="{{ $task->title }}"
    data-task-description="{{ $task->description }}"
>
    <!-- Priority Badge -->
    <div class="flex justify-between items-start mb-3">
        <div class="flex items-center gap-2 flex-1">
            @if($task->priority === 'urgent')
                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">🚨 URGENT</span>
            @elseif($task->priority === 'high')
                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">🔴 High</span>
            @elseif($task->priority === 'medium')
                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">🟡 Medium</span>
            @else
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">🟢 Low</span>
            @endif
        </div>

        <!-- Menu -->
        <div class="relative group">
            <button class="text-gray-400 hover:text-gray-600 text-lg">⋯</button>
            <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-lg shadow-lg z-10">
                <button onclick="openTaskModal({{ $task->column_id }}, {{ json_encode($task) }})" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm">✏️ Edit</button>
                <button onclick="deleteTask({{ $task->id }})" class="w-full text-left px-4 py-2 hover:bg-gray-50 text-sm text-red-600">🗑️ Delete</button>
            </div>
        </div>
    </div>

    <!-- Title -->
    <h3 data-task-title class="font-semibold text-gray-900 mb-2 line-clamp-2">
        {{ $task->title }}
    </h3>

    <!-- Description (if exists) -->
    @if($task->description)
        <p data-task-description class="text-sm text-gray-600 mb-3 line-clamp-2">
            {{ $task->description }}
        </p>
    @endif

    <!-- Tags -->
    @if($task->tags->count() > 0)
        <div class="flex flex-wrap gap-1 mb-3">
            @foreach($task->tags as $tag)
                <span
                    class="text-xs font-medium text-white px-2 py-1 rounded"
                    style="background-color: {{ $tag->color }}"
                >
                    {{ $tag->name }}
                </span>
            @endforeach
        </div>
    @endif

    <!-- Due Date -->
    @if($task->due_date)
        <div class="flex items-center text-xs text-gray-600 mb-3">
            📅
            <span class="ml-1">
                @if(now() > $task->due_date)
                    <span class="text-red-600 font-bold">{{ $task->due_date->format('M d') }} (Overdue)</span>
                @elseif(now()->addDays(3) > $task->due_date)
                    <span class="text-orange-600">{{ $task->due_date->format('M d') }}</span>
                @else
                    {{ $task->due_date->format('M d') }}
                @endif
            </span>
        </div>
    @endif

    <!-- Assignee -->
    @if($task->assignee)
        <div class="flex items-center gap-2 pt-3 border-t">
            <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">
                {{ substr($task->assignee->name, 0, 1) }}
            </div>
            <span class="text-xs text-gray-700">{{ $task->assignee->name }}</span>
        </div>
    @else
        <div class="flex items-center gap-2 pt-3 border-t text-xs text-gray-400">
            👤 Unassigned
        </div>
    @endif

    <!-- Comments & Attachments Count -->
    <div class="flex justify-between text-xs text-gray-500 mt-3">
        <span>💬 {{ $task->comments_count ?? 0 }}</span>
        <span>📎 {{ $task->attachments_count ?? 0 }}</span>
    </div>
</div>

<script>
async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) return;

    try {
        const response = await fetch(`/tasks/${taskId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (!response.ok) throw new Error('Failed to delete task');
        location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert('Error deleting task');
    }
}
</script>
