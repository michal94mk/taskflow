<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q', '');
        
        if (empty($query)) {
            return response()->json([
                'tasks' => [],
                'projects' => [],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Search tasks
        $tasks = Task::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['project', 'taskStatus', 'taskPriority'])
            ->limit(5)
            ->get()
            ->map(fn($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'type' => 'task',
                'url' => "/tasks/{$task->id}",
                'project' => $task->project?->name,
                'status' => $task->taskStatus?->name,
                'priority' => $task->taskPriority?->name,
            ]);

        // Search projects
        $projects = Project::where('user_id', $user->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(fn($project) => [
                'id' => $project->id,
                'title' => $project->name,
                'description' => $project->description,
                'type' => 'project',
                'url' => "/projects/{$project->id}",
                'status' => $project->status,
            ]);

        return response()->json([
            'tasks' => $tasks,
            'projects' => $projects,
        ]);
    }
}

