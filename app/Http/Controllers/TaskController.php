<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
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

        $query = Task::forUser($user)
            ->with(['project', 'taskStatus', 'taskPriority']);

        // Filter by project
        if ($request->filled('project_id')) {
            $query->byProject($request->project_id);
        }

        // Filter by status
        if ($request->filled('status_id')) {
            $query->byStatus($request->status_id);
        }

        // Filter by priority
        if ($request->filled('priority_id')) {
            $query->byPriority($request->priority_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
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
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
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
    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        $task->update($request->validated());

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

