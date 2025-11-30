<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Team;
use App\Models\Board;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Get total tasks statistics
     */
    public function getTotalTasksStats(?int $teamId = null): array
    {
        $cacheKey = 'analytics.total_tasks.' . ($teamId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($teamId) {
            $query = Task::query();

            if ($teamId) {
                $query->whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
            }

            $total = $query->count();
            $completed = $query->clone()->whereHas('column', function ($q) {
                $q->where('name', 'Done');
            })->count();

            $overdue = $query->clone()
                ->where('due_date', '<', now())
                ->whereHas('column', function ($q) {
                    $q->where('name', '!=', 'Done');
                })->count();

            $dueSoon = $query->clone()
                ->whereBetween('due_date', [now(), now()->addDays(7)])
                ->whereHas('column', function ($q) {
                    $q->where('name', '!=', 'Done');
                })->count();

            $completionRate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

            return [
                'total' => $total,
                'completed' => $completed,
                'in_progress' => $total - $completed,
                'overdue' => $overdue,
                'due_soon' => $dueSoon,
                'completion_rate' => $completionRate,
            ];
        });
    }

    /**
     * Get tasks by priority distribution
     */
    public function getTasksByPriority(?int $teamId = null): array
    {
        $cacheKey = 'analytics.tasks_by_priority.' . ($teamId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($teamId) {
            $query = Task::query();

            if ($teamId) {
                $query->whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
            }

            $distribution = $query
                ->select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->priority ?? 'none' => $item->count];
                })
                ->toArray();

            return [
                'high' => $distribution['high'] ?? 0,
                'medium' => $distribution['medium'] ?? 0,
                'low' => $distribution['low'] ?? 0,
                'none' => $distribution['none'] ?? 0,
            ];
        });
    }

    /**
     * Get tasks by assignee
     */
    public function getTasksByAssignee(?int $teamId = null, int $limit = 10): array
    {
        $cacheKey = 'analytics.tasks_by_assignee.' . ($teamId ?? 'all') . '.' . $limit;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($teamId, $limit) {
            $query = Task::query()
                ->select('assigned_to', DB::raw('count(*) as task_count'))
                ->whereNotNull('assigned_to')
                ->groupBy('assigned_to');

            if ($teamId) {
                $query->whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
            }

            $results = $query
                ->orderByDesc('task_count')
                ->limit($limit)
                ->get();

            return $results->map(function ($item) {
                $user = User::find($item->assigned_to);
                return [
                    'user_id' => $item->assigned_to,
                    'user_name' => $user ? $user->name : 'Unknown',
                    'user_email' => $user ? $user->email : '',
                    'task_count' => $item->task_count,
                ];
            })->toArray();
        });
    }

    /**
     * Get team statistics
     */
    public function getTeamStatistics(?int $teamId = null): array
    {
        $cacheKey = 'analytics.team_stats.' . ($teamId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($teamId) {
            if ($teamId) {
                $team = Team::with('members', 'boards')->find($teamId);

                if (!$team) {
                    return [
                        'total_members' => 0,
                        'total_boards' => 0,
                        'active_members' => 0,
                        'total_tasks' => 0,
                    ];
                }

                $totalTasks = Task::whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                })->count();

                $activeMembers = $team->members()
                    ->where('last_active_at', '>=', now()->subDays(7))
                    ->count();

                return [
                    'total_members' => $team->members->count(),
                    'total_boards' => $team->boards->count(),
                    'active_members' => $activeMembers,
                    'total_tasks' => $totalTasks,
                ];
            }

            // Global statistics
            return [
                'total_teams' => Team::count(),
                'total_users' => User::count(),
                'total_boards' => Board::count(),
                'total_tasks' => Task::count(),
            ];
        });
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(?int $teamId = null, int $limit = 20): array
    {
        $cacheKey = 'analytics.recent_activity.' . ($teamId ?? 'all') . '.' . $limit;

        return Cache::remember($cacheKey, 300, function () use ($teamId, $limit) {
            $query = ActivityLog::with('user')
                ->orderByDesc('created_at');

            if ($teamId) {
                $query->where(function ($q) use ($teamId) {
                    $q->where('model_type', 'App\\Models\\Board')
                        ->whereIn('model_id', Board::where('team_id', $teamId)->pluck('id'))
                        ->orWhere(function ($q2) use ($teamId) {
                            $q2->where('model_type', 'App\\Models\\Task')
                                ->whereIn('model_id', Task::whereHas('column.board', function ($q3) use ($teamId) {
                                    $q3->where('team_id', $teamId);
                                })->pluck('id'));
                        });
                });
            }

            return $query
                ->limit($limit)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'user_name' => $activity->user ? $activity->user->name : 'System',
                        'user_avatar' => $activity->user ? $activity->user->avatar : null,
                        'action' => $activity->action,
                        'description' => $activity->description,
                        'model_type' => class_basename($activity->model_type),
                        'created_at' => $activity->created_at->diffForHumans(),
                        'created_at_full' => $activity->created_at->format('Y-m-d H:i:s'),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get user performance metrics
     */
    public function getUserPerformanceMetrics(?int $userId = null, ?int $teamId = null): array
    {
        $cacheKey = 'analytics.user_performance.' . ($userId ?? 'all') . '.' . ($teamId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $teamId) {
            $query = Task::query();

            if ($userId) {
                $query->where('assigned_to', $userId);
            }

            if ($teamId) {
                $query->whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
            }

            $totalAssigned = $query->count();
            $completed = $query->clone()->whereHas('column', function ($q) {
                $q->where('name', 'Done');
            })->count();

            $completedOnTime = $query->clone()
                ->whereHas('column', function ($q) {
                    $q->where('name', 'Done');
                })
                ->where(function ($q) {
                    $q->whereNull('due_date')
                        ->orWhere('updated_at', '<=', DB::raw('due_date'));
                })
                ->count();

            $overdue = $query->clone()
                ->where('due_date', '<', now())
                ->whereHas('column', function ($q) {
                    $q->where('name', '!=', 'Done');
                })
                ->count();

            $avgCompletionTime = $query->clone()
                ->whereHas('column', function ($q) {
                    $q->where('name', 'Done');
                })
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
                ->value('avg_hours');

            return [
                'total_assigned' => $totalAssigned,
                'completed' => $completed,
                'in_progress' => $totalAssigned - $completed,
                'completed_on_time' => $completedOnTime,
                'overdue' => $overdue,
                'completion_rate' => $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100, 2) : 0,
                'on_time_rate' => $completed > 0 ? round(($completedOnTime / $completed) * 100, 2) : 0,
                'avg_completion_hours' => $avgCompletionTime ? round($avgCompletionTime, 2) : 0,
            ];
        });
    }

    /**
     * Generate task completion trend (last 30 days)
     */
    public function getTaskCompletionTrend(?int $teamId = null, int $days = 30): array
    {
        $cacheKey = 'analytics.completion_trend.' . ($teamId ?? 'all') . '.' . $days;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($teamId, $days) {
            $startDate = now()->subDays($days)->startOfDay();

            $query = Task::query()
                ->whereHas('column', function ($q) {
                    $q->where('name', 'Done');
                })
                ->where('updated_at', '>=', $startDate);

            if ($teamId) {
                $query->whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
            }

            $completedTasks = $query
                ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $trend = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $trend[] = [
                    'date' => $date,
                    'count' => $completedTasks->get($date)->count ?? 0,
                ];
            }

            return $trend;
        });
    }

    /**
     * Get tasks created vs completed comparison
     */
    public function getTasksCreatedVsCompleted(?int $teamId = null, int $days = 30): array
    {
        $cacheKey = 'analytics.created_vs_completed.' . ($teamId ?? 'all') . '.' . $days;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($teamId, $days) {
            $startDate = now()->subDays($days)->startOfDay();

            $createdQuery = Task::query()
                ->where('created_at', '>=', $startDate);

            $completedQuery = Task::query()
                ->whereHas('column', function ($q) {
                    $q->where('name', 'Done');
                })
                ->where('updated_at', '>=', $startDate);

            if ($teamId) {
                $createdQuery->whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
                $completedQuery->whereHas('column.board', function ($q) use ($teamId) {
                    $q->where('team_id', $teamId);
                });
            }

            $created = $createdQuery
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $completed = $completedQuery
                ->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $comparison = [];
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $comparison[] = [
                    'date' => $date,
                    'created' => $created->get($date)->count ?? 0,
                    'completed' => $completed->get($date)->count ?? 0,
                ];
            }

            return $comparison;
        });
    }

    /**
     * Clear all analytics cache
     */
    public function clearCache(): void
    {
        $patterns = [
            'analytics.total_tasks.*',
            'analytics.tasks_by_priority.*',
            'analytics.tasks_by_assignee.*',
            'analytics.team_stats.*',
            'analytics.recent_activity.*',
            'analytics.user_performance.*',
            'analytics.completion_trend.*',
            'analytics.created_vs_completed.*',
        ];

        foreach ($patterns as $pattern) {
            Cache::flush(); // In production, use more specific cache clearing
        }
    }

    /**
     * Clear specific cache by key pattern
     */
    public function clearCacheByPattern(string $pattern): void
    {
        Cache::forget($pattern);
    }
}
