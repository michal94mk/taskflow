<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();
        $projects = \App\Models\Project::all();
        $statuses = \App\Models\TaskStatus::query()->pluck('id', 'slug');
        $priorities = \App\Models\TaskPriority::query()->pluck('id', 'slug');

        if ($projects->isEmpty()) {
            return;
        }

        $tasks = [
            [
                'title' => 'Setup Laravel project',
                'description' => 'Initialize Laravel project with all necessary packages',
                'task_status_id' => $statuses['completed'] ?? null,
                'task_priority_id' => $priorities['high'] ?? null,
                'due_date' => now()->subDays(5),
                'project_id' => $projects->first()->id,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Create database models',
                'description' => 'Create Project, Task, and related models',
                'task_status_id' => $statuses['completed'] ?? null,
                'task_priority_id' => $priorities['high'] ?? null,
                'due_date' => now()->subDays(3),
                'project_id' => $projects->first()->id,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Implement dashboard',
                'description' => 'Create dashboard with KPI cards and charts',
                'task_status_id' => $statuses['in_progress'] ?? null,
                'task_priority_id' => $priorities['high'] ?? null,
                'due_date' => now()->addDays(2),
                'project_id' => $projects->first()->id,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Design UI components',
                'description' => 'Create reusable UI components for the application',
                'task_status_id' => $statuses['to_do'] ?? null,
                'task_priority_id' => $priorities['medium'] ?? null,
                'due_date' => now()->addDays(5),
                'project_id' => $projects->first()->id,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Write tests',
                'description' => 'Add unit and feature tests for all functionality',
                'task_status_id' => $statuses['to_do'] ?? null,
                'task_priority_id' => $priorities['medium'] ?? null,
                'due_date' => now()->addDays(7),
                'project_id' => $projects->first()->id,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Fix responsive design',
                'description' => 'Ensure all components work on mobile devices',
                'task_status_id' => $statuses['to_do'] ?? null,
                'task_priority_id' => $priorities['low'] ?? null,
                'due_date' => now()->addDays(10),
                'project_id' => $projects->skip(1)->first()->id,
                'user_id' => $user->id,
            ],
            [
                'title' => 'Optimize performance',
                'description' => 'Optimize database queries and frontend performance',
                'task_status_id' => $statuses['to_do'] ?? null,
                'task_priority_id' => $priorities['low'] ?? null,
                'due_date' => now()->addDays(14),
                'project_id' => $projects->skip(1)->first()->id,
                'user_id' => $user->id,
            ],
        ];

        foreach ($tasks as $task) {
            \App\Models\Task::create($task);
        }
    }
}
