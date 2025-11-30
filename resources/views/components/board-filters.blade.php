<!-- Board Filters & Controls -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <!-- Search -->
        <div class="flex-1 min-w-64">
            <div class="relative">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="🔍 Search tasks..."
                    class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                />
                <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
            </div>
        </div>

        <!-- Filter by Priority -->
        <div>
            <select
                id="priorityFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
            >
                <option value="">All Priorities</option>
                <option value="low">🟢 Low</option>
                <option value="medium">🟡 Medium</option>
                <option value="high">🔴 High</option>
                <option value="urgent">🚨 Urgent</option>
            </select>
        </div>

        <!-- Filter by Assignee -->
        <div>
            <select
                id="assigneeFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"
            >
                <option value="">All Assignees</option>
                <option value="unassigned">Unassigned</option>
                @foreach($users ?? [] as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Tags -->
        <div class="relative group">
            <button
                id="tagsFilterBtn"
                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium flex items-center gap-2"
            >
                🏷️ Tags
                <span id="tagsCount" class="bg-indigo-600 text-white rounded-full px-2 text-xs hidden">0</span>
            </button>
            <div class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-300 rounded-lg shadow-lg p-4 group-hover:block z-10">
                <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                    @foreach($tags ?? [] as $tag)
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-100 rounded cursor-pointer">
                            <input
                                type="checkbox"
                                class="tagsFilterCheckbox"
                                value="{{ $tag->id }}"
                                data-tag-name="{{ $tag->name }}"
                                class="w-4 h-4 text-indigo-600 rounded"
                            />
                            <span
                                class="text-xs font-medium px-2 py-1 rounded text-white"
                                style="background-color: {{ $tag->color }}"
                            >
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Clear Filters -->
        <button
            onclick="clearFilters()"
            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 text-sm font-medium"
        >
            ✕ Clear
        </button>
    </div>

    <!-- Active Filters Display -->
    <div id="activeFilters" class="mt-4 flex flex-wrap gap-2 hidden">
        <span class="text-sm text-gray-600">Active filters:</span>
        <div id="filterBadges" class="flex flex-wrap gap-2"></div>
    </div>
</div>

<script>
let activeFilters = {
    search: '',
    priority: '',
    assignee: '',
    tags: []
};

function applyFilters() {
    const tasks = document.querySelectorAll('[data-task-id]');
    let visibleCount = 0;

    tasks.forEach(task => {
        let show = true;

        // Search filter
        if (activeFilters.search) {
            const title = task.querySelector('[data-task-title]')?.textContent.toLowerCase() || '';
            const description = task.querySelector('[data-task-description]')?.textContent.toLowerCase() || '';
            show = show && (title.includes(activeFilters.search.toLowerCase()) ||
                          description.includes(activeFilters.search.toLowerCase()));
        }

        // Priority filter
        if (activeFilters.priority) {
            const priority = task.getAttribute('data-task-priority');
            show = show && priority === activeFilters.priority;
        }

        // Assignee filter
        if (activeFilters.assignee) {
            const assignee = task.getAttribute('data-task-assignee');
            if (activeFilters.assignee === 'unassigned') {
                show = show && !assignee;
            } else {
                show = show && assignee === activeFilters.assignee;
            }
        }

        // Tags filter
        if (activeFilters.tags.length > 0) {
            const taskTags = (task.getAttribute('data-task-tags') || '').split(',').filter(t => t);
            show = show && activeFilters.tags.some(tag => taskTags.includes(tag.toString()));
        }

        task.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    updateFilterBadges();
}

function updateFilterBadges() {
    const container = document.getElementById('filterBadges');
    const badges = [];

    if (activeFilters.search) {
        badges.push(`<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Search: ${activeFilters.search}</span>`);
    }
    if (activeFilters.priority) {
        badges.push(`<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Priority: ${activeFilters.priority}</span>`);
    }
    if (activeFilters.assignee) {
        badges.push(`<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Assignee: ${activeFilters.assignee}</span>`);
    }
    activeFilters.tags.forEach(tag => {
        badges.push(`<span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">Tag: ${tag}</span>`);
    });

    if (badges.length > 0) {
        container.innerHTML = badges.join('');
        document.getElementById('activeFilters').classList.remove('hidden');
    } else {
        document.getElementById('activeFilters').classList.add('hidden');
    }
}

function clearFilters() {
    activeFilters = { search: '', priority: '', assignee: '', tags: [] };
    document.getElementById('searchInput').value = '';
    document.getElementById('priorityFilter').value = '';
    document.getElementById('assigneeFilter').value = '';
    document.querySelectorAll('.tagsFilterCheckbox').forEach(cb => cb.checked = false);
    applyFilters();
}

// Event listeners
document.getElementById('searchInput')?.addEventListener('input', (e) => {
    activeFilters.search = e.target.value;
    applyFilters();
});

document.getElementById('priorityFilter')?.addEventListener('change', (e) => {
    activeFilters.priority = e.target.value;
    applyFilters();
});

document.getElementById('assigneeFilter')?.addEventListener('change', (e) => {
    activeFilters.assignee = e.target.value;
    applyFilters();
});

document.querySelectorAll('.tagsFilterCheckbox')?.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        activeFilters.tags = Array.from(document.querySelectorAll('.tagsFilterCheckbox:checked'))
            .map(cb => cb.value);
        applyFilters();
    });
});
</script>
