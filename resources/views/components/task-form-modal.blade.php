<!-- Task Form Modal -->
<div id="taskFormModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex justify-between items-center p-6 border-b">
            <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">Create Task</h2>
            <button onclick="closeTaskModal()" class="text-gray-500 hover:text-gray-700 text-2xl">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form id="taskForm" class="p-6 space-y-6">
            @csrf
            <input type="hidden" id="taskId" name="task_id">
            <input type="hidden" id="columnId" name="column_id">

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Task Title *
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    required
                    placeholder="Enter task title"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
                <span class="text-red-500 text-sm hidden" id="titleError"></span>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Enter task description (optional)"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"
                ></textarea>
            </div>

            <!-- Row: Priority & Due Date -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-2">
                        Priority *
                    </label>
                    <select
                        id="priority"
                        name="priority"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="">Select priority</option>
                        <option value="low">🟢 Low</option>
                        <option value="medium">🟡 Medium</option>
                        <option value="high">🔴 High</option>
                        <option value="urgent">🚨 Urgent</option>
                    </select>
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        Due Date
                    </label>
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    />
                </div>
            </div>

            <!-- Assigned To -->
            <div>
                <label for="assigned_to" class="block text-sm font-semibold text-gray-700 mb-2">
                    Assign To
                </label>
                <select
                    id="assigned_to"
                    name="assigned_to"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    <option value="">Unassigned</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tags -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Tags
                </label>
                <div id="tagsContainer" class="grid grid-cols-3 gap-2 mb-4 max-h-32 overflow-y-auto">
                    @foreach($tags ?? [] as $tag)
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded cursor-pointer">
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="{{ $tag->id }}"
                                class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500"
                            />
                            <span
                                class="text-sm font-medium px-2 py-1 rounded text-white"
                                style="background-color: {{ $tag->color }}"
                            >
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500">Select one or more tags for this task</p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button
                    type="button"
                    onclick="closeTaskModal()"
                    class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    class="px-6 py-2 text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 font-medium flex items-center gap-2"
                >
                    <span id="submitButtonText">Create Task</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTaskModal(columnId, taskData = null) {
    const modal = document.getElementById('taskFormModal');
    const form = document.getElementById('taskForm');
    const title = document.getElementById('modalTitle');
    const submitButton = document.getElementById('submitButtonText');

    // Reset form
    form.reset();
    document.getElementById('columnId').value = columnId;

    if (taskData) {
        // Edit mode
        title.textContent = 'Edit Task';
        submitButton.textContent = 'Update Task';
        document.getElementById('taskId').value = taskData.id;
        document.getElementById('title').value = taskData.title;
        document.getElementById('description').value = taskData.description || '';
        document.getElementById('priority').value = taskData.priority;
        document.getElementById('due_date').value = taskData.due_date || '';
        document.getElementById('assigned_to').value = taskData.assigned_to || '';

        // Select tags
        document.querySelectorAll('input[name="tags[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        taskData.tags?.forEach(tag => {
            const checkbox = document.querySelector(`input[name="tags[]"][value="${tag.id}"]`);
            if (checkbox) checkbox.checked = true;
        });
    } else {
        // Create mode
        title.textContent = 'Create Task';
        submitButton.textContent = 'Create Task';
        document.getElementById('taskId').value = '';
    }

    modal.classList.remove('hidden');
}

function closeTaskModal() {
    const modal = document.getElementById('taskFormModal');
    modal.classList.add('hidden');
}

document.getElementById('taskForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const taskId = document.getElementById('taskId').value;
    const columnId = document.getElementById('columnId').value;
    const formData = new FormData(e.target);

    const url = taskId
        ? `/tasks/${taskId}`
        : '/tasks';

    const method = taskId ? 'POST' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (!response.ok) throw new Error('Failed to save task');

        const task = await response.json();

        // Reload the board or update task
        location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert('Error saving task: ' + error.message);
    }
});

// Close modal when clicking outside
document.getElementById('taskFormModal')?.addEventListener('click', (e) => {
    if (e.target.id === 'taskFormModal') {
        closeTaskModal();
    }
});
</script>
