<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
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

        return Inertia::render('Dashboard', [
            'kpi' => [
                'totalTasks' => $totalTasks,
                'completedTasks' => $completedTasks,
                'inProgressTasks' => $inProgressTasks,
                'overdueTasks' => $overdueTasks,
            ],
            'recentTasks' => $recentTasks,
            'projects' => $projects,
        ]);
    }
}
