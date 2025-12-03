<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
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

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Query Scopes
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
            ->whereHas('taskStatus', fn ($q) => $q->where('slug', '!=', 'completed'));
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereHas('taskStatus', fn ($q) => $q->where('slug', 'completed'));
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereHas('taskStatus', fn ($q) => $q->where('slug', 'in_progress'));
    }

    public function scopeByProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByStatus(Builder $query, int $statusId): Builder
    {
        return $query->where('task_status_id', $statusId);
    }

    public function scopeByPriority(Builder $query, int $priorityId): Builder
    {
        return $query->where('task_priority_id', $priorityId);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        $sanitized = str_replace(['%', '_'], ['\%', '\_'], $search);
        return $query->where(function ($q) use ($sanitized) {
            $q->where('title', 'like', "%{$sanitized}%")
              ->orWhere('description', 'like', "%{$sanitized}%");
        });
    }
}
