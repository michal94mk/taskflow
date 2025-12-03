<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class KanbanController extends Controller
{
    /**
     * Display the Kanban board.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Get all statuses (cached)
        $statuses = TaskStatus::getCached();

        // Get tasks grouped by status using Query Scopes
        $tasksByStatus = [];
        foreach ($statuses as $status) {
            $query = Task::forUser($user)
                ->byStatus($status->id);

            // Apply project filter if provided
            if ($request->filled('project_id')) {
                $query->byProject($request->project_id);
            }

            $tasks = $query->with(['project', 'taskPriority'])
                ->latest()
                ->get();

            $tasksByStatus[] = [
                'status' => $status,
                'tasks' => $tasks,
            ];
        }

        // Get filter options
        $projects = $user->projects()->get(['id', 'name']);

        return Inertia::render('Kanban/Index', [
            'tasksByStatus' => $tasksByStatus,
            'projects' => $projects,
            'filter' => $request->only(['project_id']),
        ]);
    }

    /**
     * Update task status (for drag & drop).
     */
    public function updateStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        /** @var User $user */
        $user = Auth::user();

        // Verify task belongs to user
        if ($task->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $task->update($request->validated());

        return back();
    }
}

