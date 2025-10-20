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
                        <template x-for="task in column.tasks" :key="task.id">
                            <div :data-task-id="task.id"
                                 class="rounded-xl shadow-sm hover:shadow-md transition-all duration-200 p-4 mb-3 cursor-move border"
                                 :class="darkMode ? 'bg-gray-700 border-gray-600 hover:border-gray-500' : 'bg-white border-gray-100 hover:border-gray-200'">
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
                                    <button @click="deleteTask(task.id)"
                                            class="transition-colors"
                                            :class="darkMode ? 'text-gray-400 hover:text-red-400' : 'text-gray-400 hover:text-red-500'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Task Title -->
                                <h4 class="font-semibold mb-2 transition-colors" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="task.title"></h4>

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
        showBoardModal: false,
        showNewBoardModal: false,
        showNewColumnModal: false,
        showEditColumnModal: false,

        // Theme & Language
        darkMode: localStorage.getItem('darkMode') === 'true',
        locale: localStorage.getItem('locale') || 'en',

        // Forms
        newTask: {
            column_id: '',
            title: '',
            description: '',
            priority: 'medium',
            due_date: '',
            tags: []
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

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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
</style>
@endsection
