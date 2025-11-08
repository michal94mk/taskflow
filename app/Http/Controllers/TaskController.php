<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskPriority;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks.
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Task::where('user_id', $user->id)
            ->with(['project', 'taskStatus', 'taskPriority']);

        // Filter by project
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->has('status_id') && $request->status_id) {
            $query->where('task_status_id', $request->status_id);
        }

        // Filter by priority
        if ($request->has('priority_id') && $request->priority_id) {
            $query->where('task_priority_id', $request->priority_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->latest()->paginate(15);

        // Get filter options
        $projects = Project::where('user_id', $user->id)->get(['id', 'name']);
        $statuses = TaskStatus::all(['id', 'name', 'color']);
        $priorities = TaskPriority::all(['id', 'name', 'color']);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'statuses' => $statuses,
            'priorities' => $priorities,
            'filters' => $request->only(['project_id', 'status_id', 'priority_id', 'search']),
        ]);
    }

    /**
     * Show the form for creating a new task.
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();

        $projects = Project::where('user_id', $user->id)->get(['id', 'name']);
        $statuses = TaskStatus::all(['id', 'name', 'color']);
        $priorities = TaskPriority::all(['id', 'name', 'color']);

        return Inertia::render('Tasks/Create', [
            'projects' => $projects,
            'statuses' => $statuses,
            'priorities' => $priorities,
        ]);
    }

    /**
     * Store a newly created task in storage.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'task_status_id' => 'required|exists:task_statuses,id',
            'task_priority_id' => 'required|exists:task_priorities,id',
            'due_date' => 'nullable|date',
        ]);

        // Verify project belongs to user
        $project = Project::findOrFail($validated['project_id']);
        if ($project->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $task = Task::create([
            ...$validated,
            'user_id' => $user->id,
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        Gate::authorize('view', $task);

        $task->load(['project', 'taskStatus', 'taskPriority', 'comments.user']);

        return Inertia::render('Tasks/Show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        Gate::authorize('update', $task);

        /** @var User $user */
        $user = Auth::user();

        $projects = Project::where('user_id', $user->id)->get(['id', 'name']);
        $statuses = TaskStatus::all(['id', 'name', 'color']);
        $priorities = TaskPriority::all(['id', 'name', 'color']);

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'projects' => $projects,
            'statuses' => $statuses,
            'priorities' => $priorities,
        ]);
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        Gate::authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'task_status_id' => 'required|exists:task_statuses,id',
            'task_priority_id' => 'required|exists:task_priorities,id',
            'due_date' => 'nullable|date',
        ]);

        // Verify project belongs to user
        $project = Project::findOrFail($validated['project_id']);
        if ($project->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $task->update($validated);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
}

