<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Analytics Dashboard - SmartTaskBoard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Analytics Dashboard</h1>
                        <p class="text-sm text-gray-600 mt-1">Track your team's performance and progress</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Team Selector -->
                        <div class="relative">
                            <select id="teamSelector" class="block w-64 px-4 py-2 pr-8 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Teams</option>
                                @foreach($user_teams ?? [] as $team)
                                    <option value="{{ $team->id }}" {{ $selected_team_id == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button onclick="refreshDashboard()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(isset($error))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Tasks Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $total_stats['total'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $total_stats['in_progress'] ?? 0 }} in progress</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Completed Tasks Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-3xl font-bold text-green-600 mt-2">{{ $total_stats['completed'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $total_stats['completion_rate'] ?? 0 }}% completion rate</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Overdue Tasks Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Overdue</p>
                            <p class="text-3xl font-bold text-red-600 mt-2">{{ $total_stats['overdue'] ?? 0 }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $total_stats['due_soon'] ?? 0 }} due soon</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Team Members Card -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Team Members</p>
                            <p class="text-3xl font-bold text-purple-600 mt-2">
                                {{ $team_statistics['total_members'] ?? $team_statistics['total_users'] ?? 0 }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $team_statistics['active_members'] ?? 0 }} active
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Priority Distribution Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasks by Priority</h3>
                    <div class="h-64">
                        <canvas id="priorityChart"></canvas>
                    </div>
                </div>

                <!-- Assignee Distribution Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasks by Assignee</h3>
                    <div class="h-64">
                        <canvas id="assigneeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Trends Row -->
            <div class="grid grid-cols-1 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Trends (Last 30 Days)</h3>
                    <div class="h-80">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Performance and Activity Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- User Performance -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Performance</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Total Assigned</span>
                            <span class="text-lg font-bold text-gray-900">{{ $user_performance['total_assigned'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Completed</span>
                            <span class="text-lg font-bold text-green-600">{{ $user_performance['completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Completion Rate</span>
                            <span class="text-lg font-bold text-blue-600">{{ $user_performance['completion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">On-Time Rate</span>
                            <span class="text-lg font-bold text-indigo-600">{{ $user_performance['on_time_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Avg. Completion Time</span>
                            <span class="text-lg font-bold text-gray-900">{{ $user_performance['avg_completion_hours'] ?? 0 }}h</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Overdue</span>
                            <span class="text-lg font-bold text-red-600">{{ $user_performance['overdue'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @forelse($recent_activity ?? [] as $activity)
                            <div class="flex items-start space-x-3 pb-3 border-b border-gray-100 last:border-0">
                                <div class="flex-shrink-0">
                                    @if($activity['user_avatar'])
                                        <img src="{{ $activity['user_avatar'] }}" alt="{{ $activity['user_name'] }}" class="w-8 h-8 rounded-full">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-600">{{ substr($activity['user_name'], 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $activity['user_name'] }}</span>
                                        <span class="text-gray-600">{{ $activity['description'] ?? $activity['action'] }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1" title="{{ $activity['created_at_full'] }}">
                                        {{ $activity['created_at'] }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $activity['model_type'] }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 text-center py-8">No recent activity</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Chart colors
        const colors = {
            blue: 'rgb(59, 130, 246)',
            green: 'rgb(34, 197, 94)',
            yellow: 'rgb(234, 179, 8)',
            red: 'rgb(239, 68, 68)',
            purple: 'rgb(168, 85, 247)',
            indigo: 'rgb(99, 102, 241)',
            pink: 'rgb(236, 72, 153)',
            orange: 'rgb(249, 115, 22)',
        };

        // Priority Chart
        const priorityCtx = document.getElementById('priorityChart').getContext('2d');
        const priorityData = @json($priority_distribution ?? ['high' => 0, 'medium' => 0, 'low' => 0, 'none' => 0]);

        new Chart(priorityCtx, {
            type: 'doughnut',
            data: {
                labels: ['High', 'Medium', 'Low', 'None'],
                datasets: [{
                    data: [priorityData.high, priorityData.medium, priorityData.low, priorityData.none],
                    backgroundColor: [colors.red, colors.yellow, colors.blue, colors.purple],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Assignee Chart
        const assigneeCtx = document.getElementById('assigneeChart').getContext('2d');
        const assigneeData = @json($assignee_distribution ?? []);

        new Chart(assigneeCtx, {
            type: 'bar',
            data: {
                labels: assigneeData.map(item => item.user_name),
                datasets: [{
                    label: 'Tasks Assigned',
                    data: assigneeData.map(item => item.task_count),
                    backgroundColor: colors.blue,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendData = @json($created_vs_completed ?? []);

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendData.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }),
                datasets: [
                    {
                        label: 'Created',
                        data: trendData.map(item => item.created),
                        borderColor: colors.blue,
                        backgroundColor: colors.blue + '20',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Completed',
                        data: trendData.map(item => item.completed),
                        borderColor: colors.green,
                        backgroundColor: colors.green + '20',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Team selector change handler
        document.getElementById('teamSelector').addEventListener('change', function() {
            const teamId = this.value;
            const url = new URL(window.location.href);

            if (teamId) {
                url.searchParams.set('team_id', teamId);
            } else {
                url.searchParams.delete('team_id');
            }

            window.location.href = url.toString();
        });

        // Refresh dashboard
        function refreshDashboard() {
            window.location.reload();
        }

        // Auto-refresh every 5 minutes
        setInterval(refreshDashboard, 300000);
    </script>
</body>
</html>
