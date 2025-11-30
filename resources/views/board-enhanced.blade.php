@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $board->name }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $board->description }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- New Task Button -->
                    <button
                        onclick="openTaskModal({{ $board->columns->first()->id ?? 0 }})"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium flex items-center gap-2"
                    >
                        ✏️ New Task
                    </button>

                    <!-- Board Settings -->
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
                        ⚙️ Settings
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Filters -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-4">
            @include('components.board-filters', ['users' => $users, 'tags' => $tags])
        </div>
    </div>

    <!-- Main Board -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        <div class="grid gap-6" style="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));">
            @foreach($board->columns as $column)
                <div class="bg-gray-100 rounded-lg p-4 min-h-screen">
                    <!-- Column Header -->
                    <div class="flex items-center justify-between mb-4 pb-3 border-b">
                        <div class="flex items-center gap-2">
                            <h2 class="font-bold text-gray-900">{{ $column->name }}</h2>
                            <span class="bg-gray-300 text-gray-700 text-xs font-bold px-2 py-1 rounded">
                                {{ $column->tasks()->count() }}
                            </span>
                        </div>
                        <button
                            onclick="openTaskModal({{ $column->id }})"
                            class="text-gray-500 hover:text-gray-700 text-2xl"
                            title="Add task to this column"
                        >
                            +
                        </button>
                    </div>

                    <!-- Tasks Container (Droppable) -->
                    <div
                        class="space-y-3 min-h-[500px]"
                        data-column-id="{{ $column->id }}"
                        ondrop="handleDrop(event, {{ $column->id }})"
                        ondragover="event.preventDefault()"
                        ondragenter="event.target.closest('[data-column-id]').classList.add('bg-indigo-50')"
                        ondragleave="event.target.closest('[data-column-id]').classList.remove('bg-indigo-50')"
                    >
                        @forelse($column->tasks as $task)
                            @include('components.task-card', ['task' => $task])
                        @empty
                            <div class="text-center py-12 text-gray-400">
                                <p>📭 No tasks yet</p>
                                <p class="text-sm">Drag tasks here or create new ones</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- Task Form Modal -->
    @include('components.task-form-modal', ['users' => $users, 'tags' => $tags])
</div>

<script>
// Drag and Drop functionality
async function handleDrop(event, columnId) {
    event.preventDefault();
    event.target.closest('[data-column-id]')?.classList.remove('bg-indigo-50');

    const taskId = event.dataTransfer.getData('text/plain');
    if (!taskId) return;

    try {
        const response = await fetch(`/api/tasks/${taskId}/move`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                column_id: columnId,
                position: event.target.closest('[data-column-id]').children.length
            })
        });

        if (!response.ok) throw new Error('Failed to move task');
        location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert('Error moving task');
    }
}

// Add drag start listener to all tasks
document.addEventListener('dragstart', (e) => {
    if (e.target.closest('[data-task-id]')) {
        const taskId = e.target.closest('[data-task-id]').getAttribute('data-task-id');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', taskId);
    }
});
</script>

<style>
    [data-task-id] {
        transition: all 0.2s ease;
    }

    [data-task-id]:active {
        opacity: 0.8;
        transform: rotate(2deg);
    }
</style>
@endsection
