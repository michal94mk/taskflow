<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getKpiData(User $user): array
    {
        return [
            'totalTasks' => Task::where('user_id', $user->id)->count(),
            'completedTasks' => Task::where('user_id', $user->id)
                ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'completed'))
                ->count(),
            'inProgressTasks' => Task::where('user_id', $user->id)
                ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'in_progress'))
                ->count(),
            'overdueTasks' => Task::where('user_id', $user->id)
                ->where('due_date', '<', now())
                ->whereHas('taskStatus', fn ($q) => $q->where('slug', '!=', 'completed'))
                ->count(),
        ];
    }

    public function getRecentTasks(User $user, int $limit = 5)
    {
        return Task::where('user_id', $user->id)
            ->with(['project', 'taskStatus', 'taskPriority'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getRecentProjects(User $user, int $limit = 5)
    {
        return Project::where('user_id', $user->id)
            ->withCount('tasks')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getChartData(User $user): array
    {
        return [
            'tasksByStatus' => $this->getTasksByStatus($user),
            'tasksByPriority' => $this->getTasksByPriority($user),
            'timeline' => $this->getTasksTimeline($user),
        ];
    }

    private function getTasksByStatus(User $user)
    {
        return Task::where('user_id', $user->id)
            ->join('task_statuses', 'tasks.task_status_id', '=', 'task_statuses.id')
            ->selectRaw('task_statuses.name, task_statuses.color, COUNT(*) as count')
            ->groupBy('task_statuses.id', 'task_statuses.name', 'task_statuses.color')
            ->get();
    }

    private function getTasksByPriority(User $user)
    {
        return Task::where('user_id', $user->id)
            ->join('task_priorities', 'tasks.task_priority_id', '=', 'task_priorities.id')
            ->selectRaw('task_priorities.name, task_priorities.color, COUNT(*) as count')
            ->groupBy('task_priorities.id', 'task_priorities.name', 'task_priorities.color')
            ->get();
    }

    private function getTasksTimeline(User $user, int $days = 30): array
    {
        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        // Single query for created tasks
        $createdTasks = Task::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        // Single query for completed tasks
        $completedTasks = Task::where('user_id', $user->id)
            ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'completed'))
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        // Build timeline array
        $timeline = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $timeline[] = [
                'date' => $date,
                'created' => $createdTasks[$date] ?? 0,
                'completed' => $completedTasks[$date] ?? 0,
            ];
        }

        return $timeline;
    }
}

