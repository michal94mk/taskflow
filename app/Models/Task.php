<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'task_status_id',
        'task_priority_id',
        'due_date',
        'project_id',
        'user_id',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function taskStatus(): BelongsTo
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function taskPriority(): BelongsTo
    {
        return $this->belongsTo(TaskPriority::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        $statusSlug = optional($this->taskStatus)->slug;

        return $this->due_date && $this->due_date->isPast() && $statusSlug !== 'completed';
    }

    public function getPriorityColorAttribute(): string
    {
        return $this->taskPriority?->color ?? 'gray';
    }
}
