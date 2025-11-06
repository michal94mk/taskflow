<?php

namespace App\Http\Controllers;

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

        // Get all statuses
        $statuses = TaskStatus::orderBy('id')->get();

        // Get tasks grouped by status
        $tasksByStatus = [];
        foreach ($statuses as $status) {
            $tasks = Task::where('user_id', $user->id)
                ->where('task_status_id', $status->id)
                ->with(['project', 'taskPriority'])
                ->orderBy('created_at', 'desc')
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
    public function updateStatus(Request $request, Task $task)
    {
        /** @var User $user */
        $user = Auth::user();

        // Verify task belongs to user
        if ($task->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'task_status_id' => 'required|exists:task_statuses,id',
        ]);

        $task->update($validated);

        return back();
    }
}

