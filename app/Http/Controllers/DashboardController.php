<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        // KPI Statistics
        $totalTasks = Task::where('user_id', $user->id)->count();
        $completedTasks = Task::where('user_id', $user->id)
            ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'completed'))
            ->count();
        $inProgressTasks = Task::where('user_id', $user->id)
            ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'in_progress'))
            ->count();
        $overdueTasks = Task::where('user_id', $user->id)
            ->where('due_date', '<', now())
            ->whereHas('taskStatus', fn ($q) => $q->where('slug', '!=', 'completed'))
            ->count();

        // Recent Tasks
        $recentTasks = Task::where('user_id', $user->id)
            ->with(['project', 'taskStatus', 'taskPriority'])
            ->latest()
            ->limit(5)
            ->get();

        // Projects
        $projects = Project::where('user_id', $user->id)
            ->withCount('tasks')
            ->latest()
            ->limit(5)
            ->get();

        // Chart Data
        $tasksByStatus = Task::where('user_id', $user->id)
            ->join('task_statuses', 'tasks.task_status_id', '=', 'task_statuses.id')
            ->selectRaw('task_statuses.name, task_statuses.color, COUNT(*) as count')
            ->groupBy('task_statuses.id', 'task_statuses.name', 'task_statuses.color')
            ->get();

        $tasksByPriority = Task::where('user_id', $user->id)
            ->join('task_priorities', 'tasks.task_priority_id', '=', 'task_priorities.id')
            ->selectRaw('task_priorities.name, task_priorities.color, COUNT(*) as count')
            ->groupBy('task_priorities.id', 'task_priorities.name', 'task_priorities.color')
            ->get();

        // Tasks Timeline (last 30 days)
        $timelineData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $created = Task::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->count();
            $completed = Task::where('user_id', $user->id)
                ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'completed'))
                ->whereDate('updated_at', $date)
                ->count();
            
            $timelineData[] = [
                'date' => $date,
                'created' => $created,
                'completed' => $completed,
            ];
        }

        return Inertia::render('Dashboard', [
            'kpi' => [
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'inProgressTasks' => $inProgressTasks,
                'overdueTasks' => $overdueTasks,
            ],
            'recentTasks' => $recentTasks,
            'projects' => $projects,
            'charts' => [
                'tasksByStatus' => $tasksByStatus,
                'tasksByPriority' => $tasksByPriority,
                'timeline' => $timelineData,
            ],
        ]);
    }
}
