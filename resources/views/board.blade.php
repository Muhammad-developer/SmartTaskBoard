@extends('layout')

@section('content')
<div x-data="taskBoard()" x-init="init()" class="min-h-screen p-6 transition-colors duration-200" :class="darkMode ? 'bg-gray-900' : 'bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50'">
    <!-- Header -->
    <header class="mb-8 animate-fade-in">
        <div class="mx-auto">
            <div class="rounded-2xl shadow-lg border p-6 transition-colors duration-200"
                 :class="darkMode ? 'bg-gray-800/80 border-gray-700/50' : 'bg-white/80 border-white/20'">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="board.name"></h1>
                            <p class="text-sm transition-colors" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="board.description"></p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Board Selector -->
                        <button @click="showBoardModal = true"
                                class="px-4 py-2 rounded-xl transition-all duration-200 flex items-center space-x-2"
                                :class="darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                            </svg>
                            <span x-text="translate('boards')"></span>
                        </button>

                        <!-- Tags Manager -->
                        <button @click="showTagsModal = true"
                                class="px-4 py-2 rounded-xl transition-all duration-200 flex items-center space-x-2"
                                :class="darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span x-text="translate('tags')"></span>
                        </button>

                        <!-- Language Selector -->
                        <button @click="toggleLanguage()"
                                class="px-4 py-2 rounded-xl transition-all duration-200 flex items-center space-x-2"
                                :class="darkMode ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            <span x-text="locale.toUpperCase()"></span>
                        </button>

                        <!-- Theme Toggle -->
                        <button @click="toggleTheme()"
                                class="px-4 py-2 rounded-xl transition-all duration-200"
                                :class="darkMode ? 'bg-gray-700 text-yellow-400 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                            <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </button>

                        <!-- New Task Button -->
                        <button @click="showNewTaskModal = true"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg hover:scale-105 transition-all duration-200 font-medium flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span x-text="translate('new_task')"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Search and Filter Bar -->
    <div class="mb-6 animate-fade-in">
        <div class="mx-auto">
            <div class="rounded-2xl shadow-lg border p-4 transition-colors duration-200"
                 :class="darkMode ? 'bg-gray-800/80 border-gray-700/50' : 'bg-white/80 border-white/20'">
                <div class="flex items-center space-x-4">
                    <!-- Search Input -->
                    <div class="flex-1 relative">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 transition-colors"
                             :class="darkMode ? 'text-gray-400' : 'text-gray-500'"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text"
                               x-model="searchQuery"
                               class="w-full pl-10 pr-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                               :class="darkMode ? 'bg-gray-700 border-gray-600 text-white placeholder-gray-400' : 'bg-white border-gray-300 text-gray-900 placeholder-gray-500'"
                               :placeholder="translate('search')">
                    </div>

                    <!-- Priority Filter -->
                    <select x-model="filterPriority"
                            class="px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                        <option value="" x-text="translate('filter_by_priority')"></option>
                        <option value="low" x-text="translate('priority.low')"></option>
                        <option value="medium" x-text="translate('priority.medium')"></option>
                        <option value="high" x-text="translate('priority.high')"></option>
                        <option value="urgent" x-text="translate('priority.urgent')"></option>
                    </select>

                    <!-- Clear Filters -->
                    <button @click="clearFilters()"
                            x-show="searchQuery || filterPriority"
                            class="px-4 py-2 rounded-xl transition-all duration-200 flex items-center space-x-2"
                            :class="darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span x-text="translate('clear_filters')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Board -->
    <main class="mx-auto">
        <div class="flex space-x-4 overflow-x-auto pb-4">
            <template x-for="column in board.columns" :key="column.id">
                <div class="flex-shrink-0 w-80 animate-slide-up">
                    <!-- Column Header -->
                    <div class="rounded-t-2xl shadow-lg border p-4 transition-colors duration-200"
                         :class="darkMode ? 'bg-gray-800/90 border-gray-700/50' : 'bg-white/90 border-white/20'">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 rounded-full" :style="`background-color: ${column.color}`"></div>
                                <h3 class="font-semibold transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="column.name"></h3>
                                <span class="px-2 py-1 rounded-lg text-xs font-medium transition-colors"
                                      :class="darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'"
                                      x-text="column.tasks.length"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button @click="openNewTaskModal(column.id)"
                                        class="transition-colors"
                                        :class="darkMode ? 'text-gray-400 hover:text-blue-400' : 'text-gray-400 hover:text-blue-500'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                                <button @click="openEditColumnModal(column)"
                                        class="transition-colors"
                                        :class="darkMode ? 'text-gray-400 hover:text-indigo-400' : 'text-gray-400 hover:text-indigo-500'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button @click="deleteColumn(column.id)"
                                        class="transition-colors"
                                        :class="darkMode ? 'text-gray-400 hover:text-red-400' : 'text-gray-400 hover:text-red-500'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Container -->
                    <div class="rounded-b-2xl shadow-lg border border-t-0 p-4 min-h-[600px] sortable-column transition-colors duration-200"
                         :class="darkMode ? 'bg-gray-800/50 border-gray-700/50' : 'bg-white/50 border-white/20'"
                         :data-column-id="column.id">
                        <!-- Empty State -->
                        <div x-show="column.tasks.length === 0"
                             class="flex flex-col items-center justify-center py-12 text-center">
                            <svg class="w-16 h-16 mb-4 opacity-30" :class="darkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-sm mb-2 opacity-50" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="translate('no_tasks')"></p>
                            <button @click="openNewTaskModal(column.id)"
                                    class="text-sm transition-colors"
                                    :class="darkMode ? 'text-blue-400 hover:text-blue-300' : 'text-blue-500 hover:text-blue-600'"
                                    x-text="translate('add_first_task')"></button>
                        </div>

                        <template x-for="task in filteredTasks(column.tasks)" :key="task.id">
                            <div :data-task-id="task.id"
                                 class="rounded-xl shadow-sm hover:shadow-md transition-all duration-200 p-4 mb-3 cursor-move border"
                                 :class="[
                                     darkMode ? 'bg-gray-700 border-gray-600 hover:border-gray-500' : 'bg-white border-gray-100 hover:border-gray-200',
                                     isOverdue(task.due_date) ? 'border-l-4 border-l-red-500' : ''
                                 ]">
                                <!-- Task Priority Badge -->
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-1 rounded-lg text-xs font-medium"
                                          :class="{
                                              'bg-red-100 text-red-700': task.priority === 'urgent',
                                              'bg-orange-100 text-orange-700': task.priority === 'high',
                                              'bg-yellow-100 text-yellow-700': task.priority === 'medium',
                                              'bg-green-100 text-green-700': task.priority === 'low'
                                          }"
                                          x-text="translate('priority.' + task.priority).toUpperCase()"></span>
                                    <div class="flex items-center space-x-1">
                                        <button @click="openEditTaskModal(task)"
                                                class="transition-colors"
                                                :class="darkMode ? 'text-gray-400 hover:text-blue-400' : 'text-gray-400 hover:text-blue-500'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click="deleteTask(task.id)"
                                                class="transition-colors"
                                                :class="darkMode ? 'text-gray-400 hover:text-red-400' : 'text-gray-400 hover:text-red-500'">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Task Title -->
                                <h4 class="font-semibold mb-2 transition-colors cursor-pointer hover:text-blue-500"
                                    :class="darkMode ? 'text-white' : 'text-gray-900'"
                                    @click="openEditTaskModal(task)"
                                    x-text="task.title"></h4>

                                <!-- Task Description -->
                                <p class="text-sm mb-3 line-clamp-2 transition-colors"
                                   :class="darkMode ? 'text-gray-300' : 'text-gray-600'"
                                   x-show="task.description"
                                   x-text="task.description"></p>

                                <!-- Task Tags -->
                                <div class="flex flex-wrap gap-1 mb-3" x-show="task.tags && task.tags.length > 0">
                                    <template x-for="tag in task.tags" :key="tag.id">
                                        <span class="px-2 py-1 rounded-md text-xs font-medium"
                                              :style="`background-color: ${tag.color}20; color: ${tag.color}`"
                                              x-text="tag.name"></span>
                                    </template>
                                </div>

                                <!-- Task Due Date -->
                                <div class="flex items-center text-xs transition-colors" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-show="task.due_date">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span x-text="formatDate(task.due_date)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Add Column Button -->
            <div class="flex-shrink-0 w-80 animate-slide-up">
                <button @click="showNewColumnModal = true"
                        class="w-full rounded-2xl border-2 border-dashed p-8 transition-all duration-200 flex flex-col items-center justify-center min-h-[200px] group"
                        :class="darkMode ? 'border-gray-700 hover:border-gray-600 hover:bg-gray-800/50 text-gray-400 hover:text-gray-300' : 'border-gray-300 hover:border-gray-400 hover:bg-white/50 text-gray-500 hover:text-gray-700'">
                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="font-medium" x-text="translate('add_column')"></span>
                </button>
            </div>
        </div>
    </main>

    <!-- New Task Modal -->
    <div x-show="showNewTaskModal"
         x-cloak
         @click.self="showNewTaskModal = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="rounded-2xl shadow-2xl max-w-lg w-full p-6 transition-colors"
             :class="darkMode ? 'bg-gray-800' : 'bg-white'"
             @click.stop>
            <h2 class="text-2xl font-bold mb-6 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="translate('create_task')"></h2>

            <form @submit.prevent="createTask()">
                <!-- Column Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('column')"></label>
                    <select x-model="newTask.column_id"
                            required
                            class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                        <option value="" x-text="translate('select_column')"></option>
                        <template x-for="column in board.columns" :key="column.id">
                            <option :value="column.id" x-text="column.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Task Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_title')"></label>
                    <input type="text"
                           x-model="newTask.title"
                           required
                           class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                           :placeholder="translate('enter_task_title')">
                </div>

                <!-- Task Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_description')"></label>
                    <textarea x-model="newTask.description"
                              rows="3"
                              class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                              :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                              :placeholder="translate('enter_task_description')"></textarea>
                </div>

                <!-- Priority -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_priority')"></label>
                    <select x-model="newTask.priority"
                            class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                        <option value="low" x-text="translate('priority.low')"></option>
                        <option value="medium" selected x-text="translate('priority.medium')"></option>
                        <option value="high" x-text="translate('priority.high')"></option>
                        <option value="urgent" x-text="translate('priority.urgent')"></option>
                    </select>
                </div>

                <!-- Due Date -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_due_date')"></label>
                    <input type="date"
                           x-model="newTask.due_date"
                           class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                </div>

                <!-- Buttons -->
                <div class="flex space-x-3">
                    <button type="button"
                            @click="showNewTaskModal = false"
                            class="flex-1 px-4 py-2 border rounded-xl transition-colors"
                            :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                            x-text="translate('actions.cancel')"></button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-medium"
                            x-text="translate('actions.create')"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div x-show="showEditTaskModal"
         x-cloak
         @click.self="showEditTaskModal = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="rounded-2xl shadow-2xl max-w-lg w-full p-6 transition-colors"
             :class="darkMode ? 'bg-gray-800' : 'bg-white'"
             @click.stop>
            <h2 class="text-2xl font-bold mb-6 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="translate('edit_task')"></h2>

            <form @submit.prevent="updateTask()">
                <!-- Column Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('column')"></label>
                    <select x-model="editTask.column_id"
                            required
                            class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                        <template x-for="column in board.columns" :key="column.id">
                            <option :value="column.id" x-text="column.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Task Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_title')"></label>
                    <input type="text"
                           x-model="editTask.title"
                           required
                           class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                           :placeholder="translate('enter_task_title')">
                </div>

                <!-- Task Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_description')"></label>
                    <textarea x-model="editTask.description"
                              rows="3"
                              class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                              :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"
                              :placeholder="translate('enter_task_description')"></textarea>
                </div>

                <!-- Priority -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_priority')"></label>
                    <select x-model="editTask.priority"
                            class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                            :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                        <option value="low" x-text="translate('priority.low')"></option>
                        <option value="medium" x-text="translate('priority.medium')"></option>
                        <option value="high" x-text="translate('priority.high')"></option>
                        <option value="urgent" x-text="translate('priority.urgent')"></option>
                    </select>
                </div>

                <!-- Due Date -->
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('task_due_date')"></label>
                    <input type="date"
                           x-model="editTask.due_date"
                           class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                </div>

                <!-- Buttons -->
                <div class="flex space-x-3">
                    <button type="button"
                            @click="showEditTaskModal = false"
                            class="flex-1 px-4 py-2 border rounded-xl transition-colors"
                            :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                            x-text="translate('actions.cancel')"></button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-medium"
                            x-text="translate('actions.save')"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Board Management Modal -->
    <div x-show="showBoardModal"
         x-cloak
         @click.self="showBoardModal = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="rounded-2xl shadow-2xl max-w-lg w-full p-6 transition-colors"
             :class="darkMode ? 'bg-gray-800' : 'bg-white'"
             @click.stop>
            <h2 class="text-2xl font-bold mb-6 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="translate('boards')"></h2>

            <div class="mb-6 max-h-64 overflow-y-auto">
                <template x-for="b in boards" :key="b.id">
                    <div class="p-3 rounded-lg mb-2 flex justify-between items-center transition-colors"
                         :class="darkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-gray-50 hover:bg-gray-100'">
                        <a :href="`/boards/${b.id}`" class="flex-1 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="b.name"></a>
                        <span x-show="b.id === board.id" class="px-2 py-1 bg-blue-500 text-white text-xs rounded-lg">Active</span>
                    </div>
                </template>
            </div>

            <button @click="showNewBoardModal = true; showBoardModal = false"
                    class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-medium"
                    x-text="translate('new_board')"></button>
        </div>
    </div>

    <!-- New Board Modal -->
    <div x-show="showNewBoardModal"
         x-cloak
         @click.self="showNewBoardModal = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="rounded-2xl shadow-2xl max-w-lg w-full p-6 transition-colors"
             :class="darkMode ? 'bg-gray-800' : 'bg-white'"
             @click.stop>
            <h2 class="text-2xl font-bold mb-6 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="translate('create_board')"></h2>

            <form @submit.prevent="createBoard()">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('board_name')"></label>
                    <input type="text"
                           x-model="newBoard.name"
                           required
                           class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('board_description')"></label>
                    <textarea x-model="newBoard.description"
                              rows="3"
                              class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                              :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button"
                            @click="showNewBoardModal = false"
                            class="flex-1 px-4 py-2 border rounded-xl transition-colors"
                            :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                            x-text="translate('actions.cancel')"></button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-medium"
                            x-text="translate('actions.create')"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- New/Edit Column Modal -->
    <div x-show="showNewColumnModal || showEditColumnModal"
         x-cloak
         @click.self="showNewColumnModal = false; showEditColumnModal = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="rounded-2xl shadow-2xl max-w-lg w-full p-6 transition-colors"
             :class="darkMode ? 'bg-gray-800' : 'bg-white'"
             @click.stop>
            <h2 class="text-2xl font-bold mb-6 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="showEditColumnModal ? translate('edit_column') : translate('new_column')"></h2>

            <form @submit.prevent="showEditColumnModal ? updateColumn() : createColumn()">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('column_name')"></label>
                    <input type="text"
                           x-model="columnForm.name"
                           required
                           class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('column_color')"></label>
                    <input type="color"
                           x-model="columnForm.color"
                           required
                           class="w-full h-12 rounded-xl cursor-pointer">
                </div>

                <div class="flex space-x-3">
                    <button type="button"
                            @click="showNewColumnModal = false; showEditColumnModal = false"
                            class="flex-1 px-4 py-2 border rounded-xl transition-colors"
                            :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                            x-text="translate('actions.cancel')"></button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-medium"
                            x-text="translate('actions.' + (showEditColumnModal ? 'save' : 'create'))"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tags Management Modal -->
    <div x-show="showTagsModal"
         x-cloak
         @click.self="showTagsModal = false"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="rounded-2xl shadow-2xl max-w-lg w-full p-6 transition-colors"
             :class="darkMode ? 'bg-gray-800' : 'bg-white'"
             @click.stop>
            <h2 class="text-2xl font-bold mb-6 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="translate('manage_tags')"></h2>

            <!-- Tags List -->
            <div class="mb-6 max-h-64 overflow-y-auto">
                <template x-for="tag in tags" :key="tag.id">
                    <div class="p-3 rounded-lg mb-2 flex justify-between items-center transition-colors"
                         :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 rounded-full" :style="`background-color: ${tag.color}`"></div>
                            <span class="transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="tag.name"></span>
                        </div>
                        <button @click="deleteTag(tag.id)"
                                class="transition-colors"
                                :class="darkMode ? 'text-gray-400 hover:text-red-400' : 'text-gray-500 hover:text-red-500'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </template>

                <!-- Empty State for Tags -->
                <div x-show="tags.length === 0"
                     class="text-center py-8">
                    <svg class="w-12 h-12 mx-auto mb-3 opacity-30" :class="darkMode ? 'text-gray-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <p class="text-sm opacity-50" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="translate('no_tags')"></p>
                </div>
            </div>

            <!-- Create New Tag Form -->
            <form @submit.prevent="createTag()" class="border-t pt-4" :class="darkMode ? 'border-gray-700' : 'border-gray-200'">
                <h3 class="text-lg font-semibold mb-4 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="translate('new_tag')"></h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('tag_name')"></label>
                    <input type="text"
                           x-model="newTag.name"
                           required
                           class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                           :class="darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-white border-gray-300 text-gray-900'">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2 transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="translate('tag_color')"></label>
                    <input type="color"
                           x-model="newTag.color"
                           required
                           class="w-full h-12 rounded-xl cursor-pointer">
                </div>

                <div class="flex space-x-3">
                    <button type="button"
                            @click="showTagsModal = false"
                            class="flex-1 px-4 py-2 border rounded-xl transition-colors"
                            :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
                            x-text="translate('actions.cancel')"></button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl hover:shadow-lg transition-all font-medium"
                            x-text="translate('create_tag')"></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading"
         x-cloak
         class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="rounded-2xl shadow-2xl p-8 transition-colors"
             :class="darkMode ? 'bg-gray-800' : 'bg-white'">
            <div class="flex flex-col items-center">
                <svg class="w-12 h-12 text-blue-500 animate-spin mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-lg font-medium transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="translate('loading')"></p>
            </div>
        </div>
    </div>

    <!-- Keyboard Shortcuts Help -->
    <div class="fixed bottom-4 right-4 z-40" x-data="{ showHelp: false }">
        <button @click="showHelp = !showHelp"
                class="rounded-full p-3 shadow-lg transition-all duration-200"
                :class="darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-white text-gray-700 hover:bg-gray-50'">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>

        <div x-show="showHelp"
             x-cloak
             @click.away="showHelp = false"
             class="absolute bottom-16 right-0 w-64 rounded-xl shadow-2xl p-4 transition-colors"
             :class="darkMode ? 'bg-gray-800 border border-gray-700' : 'bg-white border border-gray-200'">
            <h3 class="font-semibold mb-3 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'">
                Keyboard Shortcuts
            </h3>
            <div class="space-y-2 text-sm transition-colors" :class="darkMode ? 'text-gray-300' : 'text-gray-600'">
                <div class="flex justify-between">
                    <span>New Task</span>
                    <kbd class="px-2 py-1 rounded transition-colors" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">N</kbd>
                </div>
                <div class="flex justify-between">
                    <span>Boards</span>
                    <kbd class="px-2 py-1 rounded transition-colors" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">B</kbd>
                </div>
                <div class="flex justify-between">
                    <span>Tags</span>
                    <kbd class="px-2 py-1 rounded transition-colors" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">T</kbd>
                </div>
                <div class="flex justify-between">
                    <span>Dark Mode</span>
                    <kbd class="px-2 py-1 rounded transition-colors" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">D</kbd>
                </div>
                <div class="flex justify-between">
                    <span>Language</span>
                    <kbd class="px-2 py-1 rounded transition-colors" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">L</kbd>
                </div>
                <div class="flex justify-between">
                    <span>Search</span>
                    <kbd class="px-2 py-1 rounded transition-colors" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">/</kbd>
                </div>
                <div class="flex justify-between">
                    <span>Close</span>
                    <kbd class="px-2 py-1 rounded transition-colors" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">ESC</kbd>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/translations.js"></script>
<script>
function taskBoard() {
    return {
        board: @json($board),
        boards: @json($boards),
        tags: @json($tags),

        // Modal states
        showNewTaskModal: false,
        showEditTaskModal: false,
        showBoardModal: false,
        showNewBoardModal: false,
        showNewColumnModal: false,
        showEditColumnModal: false,
        showTagsModal: false,

        // Theme & Language
        darkMode: localStorage.getItem('darkMode') === 'true',
        locale: localStorage.getItem('locale') || 'en',

        // UI States
        loading: false,
        searchQuery: '',
        filterPriority: '',
        filterTags: [],

        // Forms
        newTask: {
            column_id: '',
            title: '',
            description: '',
            priority: 'medium',
            due_date: '',
            tags: []
        },
        editTask: {
            id: null,
            column_id: '',
            title: '',
            description: '',
            priority: 'medium',
            due_date: '',
            tags: []
        },
        newTag: {
            name: '',
            color: '#3b82f6'
        },
        newBoard: {
            name: '',
            description: '',
            color: '#0ea5e9'
        },
        columnForm: {
            id: null,
            board_id: null,
            name: '',
            color: '#3b82f6'
        },

        init() {
            this.$nextTick(() => {
                this.initializeSortable();
                this.initKeyboardShortcuts();
            });
        },

        initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // Ignore if user is typing in an input
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                    return;
                }

                // N - New Task
                if (e.key === 'n' || e.key === 'N') {
                    e.preventDefault();
                    this.showNewTaskModal = true;
                }

                // B - Boards
                if (e.key === 'b' || e.key === 'B') {
                    e.preventDefault();
                    this.showBoardModal = true;
                }

                // T - Tags
                if (e.key === 't' || e.key === 'T') {
                    e.preventDefault();
                    this.showTagsModal = true;
                }

                // D - Toggle Dark Mode
                if (e.key === 'd' || e.key === 'D') {
                    e.preventDefault();
                    this.toggleTheme();
                }

                // L - Toggle Language
                if (e.key === 'l' || e.key === 'L') {
                    e.preventDefault();
                    this.toggleLanguage();
                }

                // ESC - Close modals
                if (e.key === 'Escape') {
                    this.showNewTaskModal = false;
                    this.showEditTaskModal = false;
                    this.showBoardModal = false;
                    this.showNewBoardModal = false;
                    this.showNewColumnModal = false;
                    this.showEditColumnModal = false;
                    this.showTagsModal = false;
                }

                // / - Focus search
                if (e.key === '/') {
                    e.preventDefault();
                    document.querySelector('input[x-model="searchQuery"]')?.focus();
                }
            });
        },

        // Translation
        translate(key) {
            const keys = key.split('.');
            let value = translations[this.locale];
            for (const k of keys) {
                value = value?.[k];
            }
            return value || key;
        },

        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
        },

        toggleLanguage() {
            this.locale = this.locale === 'en' ? 'ru' : 'en';
            localStorage.setItem('locale', this.locale);
        },

        initializeSortable() {
            const columns = document.querySelectorAll('.sortable-column');

            columns.forEach(columnEl => {
                new Sortable(columnEl, {
                    group: 'shared',
                    animation: 200,
                    ghostClass: 'dragging',
                    dragClass: 'drag-over',
                    onEnd: (evt) => {
                        const taskId = evt.item.dataset.taskId;
                        const newColumnId = evt.to.dataset.columnId;
                        const newPosition = evt.newIndex;

                        this.moveTask(taskId, newColumnId, newPosition);
                    }
                });
            });
        },

        async moveTask(taskId, columnId, position) {
            try {
                const response = await fetch(`/api/tasks/${taskId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ column_id: columnId, position })
                });

                if (response.ok) {
                    const updatedTask = await response.json();
                    this.updateBoardData();
                }
            } catch (error) {
                console.error('Error moving task:', error);
            }
        },

        async createTask() {
            try {
                const response = await fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newTask)
                });

                if (response.ok) {
                    const task = await response.json();
                    this.updateBoardData();
                    this.showNewTaskModal = false;
                    this.resetNewTask();
                }
            } catch (error) {
                console.error('Error creating task:', error);
            }
        },

        async deleteTask(taskId) {
            if (!confirm(this.translate('confirm.delete_task'))) return;

            try {
                const response = await fetch(`/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    this.updateBoardData();
                }
            } catch (error) {
                console.error('Error deleting task:', error);
            }
        },

        // Board Management
        async createBoard() {
            try {
                const response = await fetch('/boards', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newBoard)
                });

                if (response.ok) {
                    const board = await response.json();
                    window.location.href = `/boards/${board.id}`;
                }
            } catch (error) {
                console.error('Error creating board:', error);
            }
        },

        // Column Management
        async createColumn() {
            try {
                this.columnForm.board_id = this.board.id;
                const response = await fetch('/columns', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.columnForm)
                });

                if (response.ok) {
                    this.updateBoardData();
                    this.showNewColumnModal = false;
                    this.resetColumnForm();
                }
            } catch (error) {
                console.error('Error creating column:', error);
            }
        },

        openEditColumnModal(column) {
            this.columnForm = {
                id: column.id,
                board_id: column.board_id,
                name: column.name,
                color: column.color
            };
            this.showEditColumnModal = true;
        },

        async updateColumn() {
            try {
                const response = await fetch(`/columns/${this.columnForm.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        name: this.columnForm.name,
                        color: this.columnForm.color
                    })
                });

                if (response.ok) {
                    this.updateBoardData();
                    this.showEditColumnModal = false;
                    this.resetColumnForm();
                }
            } catch (error) {
                console.error('Error updating column:', error);
            }
        },

        async deleteColumn(columnId) {
            if (!confirm(this.translate('confirm.delete_column'))) return;

            try {
                const response = await fetch(`/columns/${columnId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    this.updateBoardData();
                }
            } catch (error) {
                console.error('Error deleting column:', error);
            }
        },

        resetColumnForm() {
            this.columnForm = {
                id: null,
                board_id: null,
                name: '',
                color: '#3b82f6'
            };
        },

        async updateBoardData() {
            try {
                const response = await fetch(`/api/boards/${this.board.id}/data`);
                if (response.ok) {
                    this.board = await response.json();
                    this.$nextTick(() => {
                        this.initializeSortable();
                    });
                }
            } catch (error) {
                console.error('Error updating board data:', error);
            }
        },

        openNewTaskModal(columnId) {
            this.newTask.column_id = columnId;
            this.showNewTaskModal = true;
        },

        resetNewTask() {
            this.newTask = {
                column_id: '',
                title: '',
                description: '',
                priority: 'medium',
                due_date: '',
                tags: []
            };
        },

        openEditTaskModal(task) {
            this.editTask = {
                id: task.id,
                column_id: task.column_id,
                title: task.title,
                description: task.description || '',
                priority: task.priority,
                due_date: task.due_date ? task.due_date.split('T')[0] : '',
                tags: task.tags ? task.tags.map(t => t.id) : []
            };
            this.showEditTaskModal = true;
        },

        async updateTask() {
            try {
                this.loading = true;
                const response = await fetch(`/tasks/${this.editTask.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        title: this.editTask.title,
                        description: this.editTask.description,
                        priority: this.editTask.priority,
                        due_date: this.editTask.due_date,
                        tags: this.editTask.tags
                    })
                });

                if (response.ok) {
                    await this.updateBoardData();
                    this.showEditTaskModal = false;
                    this.showToast(this.translate('messages.task_updated'), 'success');
                }
            } catch (error) {
                console.error('Error updating task:', error);
                this.showToast('Error updating task', 'error');
            } finally {
                this.loading = false;
            }
        },

        isOverdue(dueDate) {
            if (!dueDate) return false;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const due = new Date(dueDate);
            due.setHours(0, 0, 0, 0);
            return due < today;
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const locale = this.locale === 'ru' ? 'ru-RU' : 'en-US';
            return date.toLocaleDateString(locale, { month: 'short', day: 'numeric', year: 'numeric' });
        },

        showToast(message, type = 'info') {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg z-50 transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                'bg-blue-500'
            } text-white`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        },

        // Search and Filter
        filteredTasks(tasks) {
            let filtered = tasks;

            // Search filter
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(task =>
                    task.title.toLowerCase().includes(query) ||
                    (task.description && task.description.toLowerCase().includes(query))
                );
            }

            // Priority filter
            if (this.filterPriority) {
                filtered = filtered.filter(task => task.priority === this.filterPriority);
            }

            return filtered;
        },

        clearFilters() {
            this.searchQuery = '';
            this.filterPriority = '';
        },

        // Tag Management
        async createTag() {
            try {
                const response = await fetch('/tags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.newTag)
                });

                if (response.ok) {
                    const tag = await response.json();
                    this.tags.push(tag);
                    this.newTag = { name: '', color: '#3b82f6' };
                    this.showToast(this.translate('messages.tag_created'), 'success');
                }
            } catch (error) {
                console.error('Error creating tag:', error);
                this.showToast('Error creating tag', 'error');
            }
        },

        async deleteTag(tagId) {
            if (!confirm(this.translate('confirm.delete_tag'))) return;

            try {
                const response = await fetch(`/tags/${tagId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    this.tags = this.tags.filter(t => t.id !== tagId);
                    this.showToast(this.translate('messages.tag_deleted'), 'success');
                }
            } catch (error) {
                console.error('Error deleting tag:', error);
                this.showToast('Error deleting tag', 'error');
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Mobile optimization */
@media (max-width: 768px) {
    .sortable-column {
        min-height: 400px !important;
    }

    /* Touch-friendly drag handles */
    .cursor-move {
        cursor: grab;
        -webkit-user-select: none;
        user-select: none;
        touch-action: none;
    }

    .cursor-move:active {
        cursor: grabbing;
    }
}

/* Animations */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animate-slide-up {
    animation: slide-up 0.4s ease-out;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.5);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.7);
}

/* Dark mode scrollbar */
.dark ::-webkit-scrollbar-thumb {
    background: rgba(75, 85, 99, 0.5);
}

.dark ::-webkit-scrollbar-thumb:hover {
    background: rgba(75, 85, 99, 0.7);
}
</style>
@endsection
