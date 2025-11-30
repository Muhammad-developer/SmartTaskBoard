<!-- Task Details Modal - Full-featured Trello-like Interface -->
<div x-show="showTaskDetailsModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:leave="transition ease-in duration-200"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
     @click.self="showTaskDetailsModal = false"
     @keydown.escape="showTaskDetailsModal = false">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col"
         @click.stop
         x-transition:enter="transition ease-out duration-300"
         x-transition:leave="transition ease-in duration-200">

        <!-- Header with Close Button -->
        <div class="flex items-center justify-between px-8 py-6 border-b dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white" x-text="editingTask.title || 'Task Details'"></h2>
            <button @click="showTaskDetailsModal = false" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-2xl">×</button>
        </div>

        <!-- Main Content Area - Two Column Layout -->
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-3 gap-6 p-8">

                <!-- Left Column: Main Task Content (2/3 width) -->
                <div class="col-span-2 space-y-6">

                    <!-- Task Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ trans('messages.task_title') ?? 'Title' }}</label>
                        <input type="text"
                               x-model="editingTask.title"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors"
                               placeholder="Task title">
                    </div>

                    <!-- Cover Image Section -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">📸 Cover Image</label>
                        <div class="relative">
                            <!-- Cover Image Preview -->
                            <div x-show="editingTask.cover_image" class="mb-3 relative">
                                <img :src="`/storage/${editingTask.cover_image}`" alt="Cover" class="w-full h-40 object-cover rounded-lg">
                                <button @click="deleteCoverImage()"
                                        class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:border-blue-500 transition-colors"
                                 @click="document.getElementById('coverImageInput').click()"
                                 x-show="!editingTask.cover_image">
                                <input type="file"
                                       id="coverImageInput"
                                       class="hidden"
                                       @change="handleCoverImageUpload($event)"
                                       accept="image/*">
                                <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Click to upload cover image</p>
                            </div>
                        </div>
                    </div>

                    <!-- Task Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ trans('messages.task_description') ?? 'Description' }}</label>
                        <textarea
                               x-model="editingTask.description"
                               rows="6"
                               class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors"
                               placeholder="Add a detailed description..."></textarea>
                    </div>

                    <!-- Checklists Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">✓ Checklists</h3>
                            <button @click="addChecklist()"
                                    class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                                + Add Checklist
                            </button>
                        </div>

                        <template x-for="(checklist, idx) in editingTask.checklists || []" :key="idx">
                            <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg space-y-3">
                                <div class="flex items-center justify-between">
                                    <input type="text"
                                           x-model="checklist.title"
                                           class="flex-1 px-3 py-2 border rounded bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white font-medium"
                                           placeholder="Checklist title">
                                    <button @click="editingTask.checklists.splice(idx, 1)"
                                            class="text-red-500 hover:text-red-700 ml-2">🗑️</button>
                                </div>

                                <template x-for="(item, itemIdx) in checklist.items || []" :key="itemIdx">
                                    <div class="flex items-center gap-3 pl-2">
                                        <input type="checkbox"
                                               x-model="item.completed"
                                               class="w-4 h-4 text-blue-600 rounded focus:ring-2">
                                        <input type="text"
                                               x-model="item.text"
                                               :class="item.completed ? 'line-through text-gray-400' : ''"
                                               class="flex-1 px-2 py-1 border rounded bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm"
                                               placeholder="Checklist item">
                                        <button @click="checklist.items.splice(itemIdx, 1)"
                                                class="text-gray-400 hover:text-gray-600">×</button>
                                    </div>
                                </template>

                                <button @click="addChecklistItem(idx)"
                                        class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 text-sm">
                                    + Add item
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Attachments Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📎 Attachments</h3>
                        </div>

                        <!-- File Upload Area -->
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center mb-4 cursor-pointer hover:border-blue-500 transition-colors"
                             @click="document.getElementById('fileInput').click()">
                            <input type="file"
                                   id="fileInput"
                                   class="hidden"
                                   @change="handleFileUpload($event)"
                                   multiple>
                            <svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-gray-600 dark:text-gray-400">Drag and drop or click to upload</p>
                        </div>

                        <!-- Existing Attachments -->
                        <template x-if="editingTask.attachments && editingTask.attachments.length > 0">
                            <div class="space-y-2">
                                <template x-for="attachment in editingTask.attachments" :key="attachment.id">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center gap-3 flex-1">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="attachment.file_name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400" x-text="`${(attachment.file_size / 1024).toFixed(2)} KB`"></p>
                                            </div>
                                        </div>
                                        <button @click="deleteAttachment(attachment.id)"
                                                class="text-red-500 hover:text-red-700">🗑️</button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Comments Section -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">💬 Comments</h3>

                        <!-- Comment Input -->
                        <div class="flex gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <textarea
                                       x-model="newComment"
                                       rows="3"
                                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-colors"
                                       placeholder="Add a comment..."></textarea>
                                <div class="mt-2 flex gap-2">
                                    <button @click="addComment()"
                                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                        Comment
                                    </button>
                                    <button @click="newComment = ''"
                                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Comments -->
                        <template x-if="editingTask.comments && editingTask.comments.length > 0">
                            <div class="space-y-4">
                                <template x-for="comment in editingTask.comments" :key="comment.id">
                                    <div class="flex gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="comment.author?.name || 'Unknown'"></p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1" x-text="new Date(comment.created_at).toLocaleDateString()"></p>
                                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="comment.content"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                </div>

                <!-- Right Sidebar (1/3 width) -->
                <div class="col-span-1 space-y-6">

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Priority</label>
                        <div class="space-y-2">
                            <template x-for="priority in ['low', 'medium', 'high', 'urgent']" :key="priority">
                                <button @click="editingTask.priority = priority"
                                        :class="editingTask.priority === priority ? 'ring-2 ring-offset-2 ring-blue-500' : ''"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-500 transition-all text-sm font-medium text-left dark:bg-gray-700 dark:text-white"
                                        :style="{
                                            'backgroundColor': editingTask.priority === priority ? (priority === 'urgent' ? '#fee2e2' : priority === 'high' ? '#fecaca' : priority === 'medium' ? '#fef3c7' : '#dcfce7') : '',
                                            'color': editingTask.priority === priority ? (priority === 'urgent' ? '#dc2626' : priority === 'high' ? '#ea580c' : priority === 'medium' ? '#b45309' : '#15803d') : ''
                                        }"
                                        x-text="priority.charAt(0).toUpperCase() + priority.slice(1)">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Assignee(s) -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Assignees</label>
                        <div class="space-y-2">
                            <template x-for="user in users" :key="user.id">
                                <button @click="toggleAssignee(user.id)"
                                        :class="editingTask.assignees && editingTask.assignees.includes(user.id) ? 'ring-2 ring-offset-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700'"
                                        class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 transition-all text-sm font-medium text-left dark:bg-gray-700 dark:text-white flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full text-white text-xs font-bold flex items-center justify-center flex-shrink-0"
                                         :style="`background-color: hsl(${user.id * 30}, 70%, 60%)`"
                                         x-text="user.name.charAt(0).toUpperCase()"></div>
                                    <span x-text="user.name"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Due Date</label>
                        <div class="flex gap-2">
                            <input type="date"
                                   x-model="editingTask.due_date"
                                   class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <button x-show="editingTask.due_date"
                                    @click="editingTask.due_date = null"
                                    class="px-3 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                Clear
                            </button>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Tags</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <template x-for="tag in tags" :key="tag.id">
                                <button @click="toggleTag(tag.id)"
                                        :class="editingTask.tags && editingTask.tags.includes(tag.id) ? 'ring-2 ring-offset-2 ring-blue-500' : 'hover:opacity-80'"
                                        class="w-full px-3 py-2 rounded-lg transition-all text-sm font-medium text-white text-left"
                                        :style="`background-color: ${tag.color}`"
                                        x-text="tag.name">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Time Estimation -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">⏱️ Time Estimate</label>
                        <div class="flex gap-2">
                            <input type="number"
                                   x-model="editingTask.estimated_hours"
                                   min="0"
                                   step="0.5"
                                   class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   placeholder="Hours">
                            <span class="self-center text-gray-600 dark:text-gray-400">hours</span>
                        </div>
                        <template x-if="editingTask.time_spent">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                                Spent: <span x-text="editingTask.time_spent"></span> hours
                            </p>
                        </template>
                    </div>

                    <!-- Column -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Column</label>
                        <select x-model="editingTask.column_id"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <template x-for="column in board.columns || []" :key="column.id">
                                <option :value="column.id" x-text="column.name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Activity / Metadata -->
                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1 pt-4 border-t dark:border-gray-700">
                        <p>Created: <span x-text="new Date(editingTask.created_at).toLocaleDateString()"></span></p>
                        <p>Updated: <span x-text="new Date(editingTask.updated_at).toLocaleDateString()"></span></p>
                    </div>

                </div>

            </div>
        </div>

        <!-- Footer with Actions -->
        <div class="flex items-center justify-between px-8 py-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <div class="flex gap-2">
                <button @click="deleteCurrentTask()"
                        class="px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg font-medium transition-colors">
                    Delete Task
                </button>
                <button x-show="!editingTask.archived"
                        @click="archiveTask()"
                        class="px-4 py-2 text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg font-medium transition-colors">
                    Archive
                </button>
                <button x-show="editingTask.archived"
                        @click="restoreTask()"
                        class="px-4 py-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg font-medium transition-colors">
                    Restore
                </button>
            </div>
            <div class="flex gap-3">
                <button @click="showTaskDetailsModal = false"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                    Cancel
                </button>
                <button @click="saveTaskDetails()"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
