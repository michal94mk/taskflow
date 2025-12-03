<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getProgressAttribute(): int
    {
        // Use the cached tasks_count from withCount('tasks')
        $totalTasks = $this->tasks_count ?? $this->tasks()->count();
        
        if ($totalTasks === 0) {
            return 0;
        }

        // Use cached completed_tasks_count if available, otherwise query
        $completedTasks = $this->completed_tasks_count ?? $this->tasks()
            ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'completed'))
            ->count();

        return round(($completedTasks / $totalTasks) * 100);
    }

    /**
     * Get the progress as decimal (0.0 - 1.0) for calculations
     */
    public function getProgressDecimalAttribute(): float
    {
        $totalTasks = $this->tasks_count ?? $this->tasks()->count();
        
        if ($totalTasks === 0) {
            return 0.0;
        }

        $completedTasks = $this->completed_tasks_count ?? $this->tasks()
            ->whereHas('taskStatus', fn ($q) => $q->where('slug', 'completed'))
            ->count();

        return $completedTasks / $totalTasks;
    }
}
