<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskStatus;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        $this->updateProjectCompletedCount($task);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        // Check if status changed
        if ($task->isDirty('task_status_id')) {
            $this->updateProjectCompletedCount($task);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $this->updateProjectCompletedCount($task);
    }

    /**
     * Update the completed tasks count for the project
     */
    private function updateProjectCompletedCount(Task $task): void
    {
        if (!$task->project_id) {
            return;
        }

        $completedStatusId = TaskStatus::where('slug', 'completed')->first()?->id;
        
        if (!$completedStatusId) {
            return;
        }

        $completedCount = Task::where('project_id', $task->project_id)
            ->where('task_status_id', $completedStatusId)
            ->count();

        $task->project()->update([
            'completed_tasks_count' => $completedCount,
        ]);
    }
}

