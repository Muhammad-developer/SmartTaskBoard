<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the analytics dashboard
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $teamId = $request->query('team_id');

            // Validate team access if team_id is provided
            if ($teamId) {
                $team = \App\Models\Team::find($teamId);
                if (!$team || !$user->canAccessTeam($team)) {
                    abort(403, 'You do not have access to this team.');
                }
            }

            // Get all analytics data
            $data = [
                'total_stats' => $this->getTotalStatistics($teamId),
                'priority_distribution' => $this->getPriorityDistribution($teamId),
                'assignee_distribution' => $this->getAssigneeDistribution($teamId),
                'team_statistics' => $this->getTeamStatistics($teamId),
                'recent_activity' => $this->getRecentActivity($teamId),
                'user_performance' => $this->getUserPerformanceMetrics($user->id, $teamId),
                'completion_trend' => $this->getCompletionTrend($teamId),
                'created_vs_completed' => $this->getCreatedVsCompleted($teamId),
                'user_teams' => $user->teams()->get(),
                'selected_team_id' => $teamId,
            ];

            return view('dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return view('dashboard', [
                'error' => 'An error occurred while loading dashboard data.',
                'total_stats' => $this->getEmptyStats(),
                'priority_distribution' => ['high' => 0, 'medium' => 0, 'low' => 0, 'none' => 0],
                'assignee_distribution' => [],
                'team_statistics' => [],
                'recent_activity' => [],
                'user_performance' => $this->getEmptyPerformanceMetrics(),
                'completion_trend' => [],
                'created_vs_completed' => [],
                'user_teams' => [],
                'selected_team_id' => null,
            ]);
        }
    }

    /**
     * Get total tasks statistics
     */
    private function getTotalStatistics(?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getTotalTasksStats($teamId);
        } catch (\Exception $e) {
            Log::error('Error getting total statistics: ' . $e->getMessage());
            return $this->getEmptyStats();
        }
    }

    /**
     * Get tasks by priority distribution
     */
    private function getPriorityDistribution(?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getTasksByPriority($teamId);
        } catch (\Exception $e) {
            Log::error('Error getting priority distribution: ' . $e->getMessage());
            return ['high' => 0, 'medium' => 0, 'low' => 0, 'none' => 0];
        }
    }

    /**
     * Get tasks by assignee
     */
    private function getAssigneeDistribution(?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getTasksByAssignee($teamId, 10);
        } catch (\Exception $e) {
            Log::error('Error getting assignee distribution: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get team statistics
     */
    private function getTeamStatistics(?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getTeamStatistics($teamId);
        } catch (\Exception $e) {
            Log::error('Error getting team statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getRecentActivity($teamId, 15);
        } catch (\Exception $e) {
            Log::error('Error getting recent activity: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user performance metrics
     */
    private function getUserPerformanceMetrics(int $userId, ?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getUserPerformanceMetrics($userId, $teamId);
        } catch (\Exception $e) {
            Log::error('Error getting user performance metrics: ' . $e->getMessage());
            return $this->getEmptyPerformanceMetrics();
        }
    }

    /**
     * Get task completion trend
     */
    private function getCompletionTrend(?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getTaskCompletionTrend($teamId, 30);
        } catch (\Exception $e) {
            Log::error('Error getting completion trend: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get tasks created vs completed
     */
    private function getCreatedVsCompleted(?int $teamId = null): array
    {
        try {
            return $this->analyticsService->getTasksCreatedVsCompleted($teamId, 30);
        } catch (\Exception $e) {
            Log::error('Error getting created vs completed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * API endpoint for getting dashboard statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $user = Auth::user();
            $teamId = $request->query('team_id');

            // Validate team access
            if ($teamId) {
                $team = \App\Models\Team::find($teamId);
                if (!$team || !$user->canAccessTeam($team)) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_stats' => $this->getTotalStatistics($teamId),
                    'priority_distribution' => $this->getPriorityDistribution($teamId),
                    'assignee_distribution' => $this->getAssigneeDistribution($teamId),
                    'team_statistics' => $this->getTeamStatistics($teamId),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('API statistics error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch statistics'], 500);
        }
    }

    /**
     * API endpoint for getting recent activity
     */
    public function getActivity(Request $request)
    {
        try {
            $user = Auth::user();
            $teamId = $request->query('team_id');
            $limit = $request->query('limit', 15);

            // Validate team access
            if ($teamId) {
                $team = \App\Models\Team::find($teamId);
                if (!$team || !$user->canAccessTeam($team)) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $this->analyticsService->getRecentActivity($teamId, $limit),
            ]);
        } catch (\Exception $e) {
            Log::error('API activity error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch activity'], 500);
        }
    }

    /**
     * API endpoint for getting user performance
     */
    public function getUserPerformance(Request $request)
    {
        try {
            $user = Auth::user();
            $userId = $request->query('user_id', $user->id);
            $teamId = $request->query('team_id');

            // Only allow viewing own performance unless admin
            if ($userId != $user->id) {
                if (!$teamId) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                $team = \App\Models\Team::find($teamId);
                if (!$team || !$user->isTeamAdmin($team)) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $this->analyticsService->getUserPerformanceMetrics($userId, $teamId),
            ]);
        } catch (\Exception $e) {
            Log::error('API user performance error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch user performance'], 500);
        }
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(Request $request)
    {
        try {
            $user = Auth::user();

            // Only admins can clear cache globally
            // For team-specific, check team admin
            $teamId = $request->input('team_id');

            if ($teamId) {
                $team = \App\Models\Team::find($teamId);
                if (!$team || !$user->isTeamAdmin($team)) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }

            $this->analyticsService->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Cache clear error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to clear cache'], 500);
        }
    }

    /**
     * Get empty statistics array
     */
    private function getEmptyStats(): array
    {
        return [
            'total' => 0,
            'completed' => 0,
            'in_progress' => 0,
            'overdue' => 0,
            'due_soon' => 0,
            'completion_rate' => 0,
        ];
    }

    /**
     * Get empty performance metrics
     */
    private function getEmptyPerformanceMetrics(): array
    {
        return [
            'total_assigned' => 0,
            'completed' => 0,
            'in_progress' => 0,
            'completed_on_time' => 0,
            'overdue' => 0,
            'completion_rate' => 0,
            'on_time_rate' => 0,
            'avg_completion_hours' => 0,
        ];
    }
}
